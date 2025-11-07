<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\House;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function index()
    {
        // Get counts (these are optimized - they don't load data)
        $totalUsers = User::count();
        $totalLandlords = Landlord::count();
        $totalTenants = Tenant::count();
        $totalHouses = House::count();
        $availableHouses = House::where('status', 'available')->count();
        $occupiedHouses = House::where('status', 'occupied')->count();
        $totalCategories = Category::count();

        // Get monthly data for charts (current year)
        $monthlyTenants = Tenant::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyHouses = House::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $tenantData = array_replace(array_fill(1, 12, 0), $monthlyTenants);
        $houseData = array_replace(array_fill(1, 12, 0), $monthlyHouses);

        $housesByCategory = House::selectRaw('category, SUM(quantity) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // ✅ FIX: Only select needed columns for recent tenants
        $recentTenants = Tenant::with([
            'user:id,name,email',
            'house:id,title,price'
        ])
            ->latest()
            ->take(5)
            ->get();

        // ✅ FIX: Only select needed columns for top landlords
        $topLandlords = Landlord::withCount('houses')
            ->with('user:id,name,email,phone')
            ->orderBy('houses_count', 'desc')
            ->take(6)
            ->get();

        // Totals
        $totalRevenue = Tenant::sum('rent');
        $totalUtilities = Tenant::sum('utilities');
        $totalDeposits = Tenant::sum('security_deposit');

        return view('admin.body', compact(
            'totalUsers',
            'totalLandlords',
            'totalTenants',
            'totalHouses',
            'availableHouses',
            'occupiedHouses',
            'totalCategories',
            'tenantData',
            'houseData',
            'housesByCategory',
            'recentTenants',
            'topLandlords',
            'totalRevenue',
            'totalUtilities',
            'totalDeposits'
        ));
    }

    // Category
    public function view_category(){
        $data = Category::paginate(15); // ✅ FIX: Use pagination
        return view('admin.category.category', compact('data'));
    }

    public function add_category(Request $request){
        $category = new Category;
        $category->category_name = $request->category;
        $category->save();

        toastr()->closeButton()->success('Category added successfully.');
        return redirect()->back();
    }

    public function delete_category($id){
        $data = Category::find($id);
        $data->delete();

        toastr()->closeButton()->success('Category deleted successfully.');
        return redirect()->back();
    }

    public function edit_category($id){
        $data = Category::find($id);
        return view('admin.category.edit_category', compact('data'));
    }

    public function update_category(Request $request, $id){
        $data = Category::find($id);
        $data->category_name = $request->category;
        $data->save();

        toastr()->closeButton()->success('Category updated successfully.');
        return redirect('/view_category');
    }

    // House
    public function add_house(){
        $category = Category::all();
        // ✅ FIX: Only select needed columns
        $landlords = Landlord::with('user:id,name,email')->get();
        return view('admin.house.add_house', compact('category', 'landlords'));
    }

    public function upload_house(Request $request){
        $data = new House();
        $data->title = $request->title;
        $data->description = $request->description;
        $data->price = $request->price;
        $data->location = $request->location;
        $data->category = $request->category;
        $data->quantity = $request->quantity;
        $data->landlord_id = $request->landlord_id;
        $data->status = $request->status ?? 'available';

        $image = $request->image;
        if($image){
            $imagename = time().'.'.$image->getClientOriginalExtension();
            $request->image->move('houses', $imagename);
            $data->image = $imagename;
        }

        $data->save();
        toastr()->closeButton()->success('House uploaded successfully.');
        return redirect()->back();
    }

    public function view_house(){
        // ✅ FIX: Only load needed landlord data
        $house = House::with('landlord.user:id,name')->paginate(10);
        return view('admin.house.view_house', compact('house'));
    }

    public function delete_house($id){
        $house = House::find($id);
        $image_path = public_path('houses/'.$house->image);
        if(file_exists($image_path)){
            unlink($image_path);
        }
        $house->delete();
        toastr()->closeButton()->success('House deleted successfully.');
        return redirect()->back();
    }

    public function edit_house($id)
    {
        $house = House::find($id);
        $category = Category::all();
        $landlords = Landlord::with('user:id,name')->get();
        return view('admin.house.edit_house', compact('house', 'category', 'landlords'));
    }

    public function update_house(Request $request, $id){
        $house = House::find($id);
        $house->title = $request->title;
        $house->description = $request->description;
        $house->price = $request->price;
        $house->location = $request->location;
        $house->category = $request->category;
        $house->quantity = $request->quantity;
        $house->landlord_id = $request->landlord_id;
        $house->status = $request->status ?? $house->status;

        $image = $request->image;
        if($image){
            $old_image_path = public_path('houses/'.$house->image);
            if(file_exists($old_image_path) && $house->image){
                unlink($old_image_path);
            }
            $imagename = time().'.'.$image->getClientOriginalExtension();
            $request->image->move('houses', $imagename);
            $house->image = $imagename;
        }

        $house->save();
        toastr()->closeButton()->success('House updated successfully.');
        return redirect('/view_house');
    }

    public function search_house(Request $request){
        $search = $request->search;
        $house = House::where('title', 'LIKE', '%'.$search.'%')
            ->with('landlord.user:id,name')
            ->paginate(10);
        return view('admin.house.view_house', compact('house'));
    }

    // Users
    public function view_user(){
        $data = User::paginate(15); // ✅ FIX: Use pagination
        return view('admin.user.users', compact('data'));
    }

    public function add_user(){
        return view('admin.user.add_user');
    }

    public function upload_user(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'password' => 'required|min:6',
            'usertype' => 'required|in:user,admin',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->usertype = $request->usertype;
        $user->password = Hash::make($request->password);
        $user->save();

        $loginUrl = url('/login');
        Mail::raw("Hello {$user->name},\n\nYour account has been created successfully.\nYou can access the system here: {$loginUrl}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Welcome to the System');
        });
        toastr()->closeButton()->success('User added successfully and email sent.');

        return redirect()->back();
    }

    public function delete_user($id){
        $data = User::find($id);
        $data->delete();

        toastr()->closeButton()->success('User deleted successfully.');
        return redirect()->back();
    }

    public function edit_user($id){
        $data = User::find($id);
        return view('admin.user.edit_user', compact('data'));
    }

    public function update_user(Request $request, $id){
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->usertype = $request->usertype;

        if ($request->filled('password')) {
            $data->password = Hash::make($request->password);
        }

        $data->save();
        toastr()->closeButton()->success('User updated successfully.');
        return redirect('/view_user');
    }

    public function add_tenant(){
        $users = User::where('usertype', 'user')
                    ->whereNotIn('id', Tenant::pluck('user_id'))
                    ->select('id', 'name', 'email') // ✅ FIX: Only select needed columns
                    ->get();

        $houses = House::whereNotIn('id', Tenant::pluck('house_id'))
                      ->select('id', 'title', 'price') // ✅ FIX
                      ->get();

        $landlords = User::where('usertype', 'landlord')
                        ->select('id', 'name') // ✅ FIX
                        ->get();

        return view('admin.tenant.add_tenant', compact('users', 'houses', 'landlords'));
    }

    public function upload_tenant(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'house_id' => 'required|exists:houses,id',
            'landlord_id' => 'nullable|exists:users,id',
            'national_id' => 'nullable|string|max:255',
            'rent' => 'required|numeric|min:0',
            'utilities' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'lease_start_date' => 'nullable|date',
            'lease_end_date' => 'nullable|date|after_or_equal:lease_start_date',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        try {
            $user = User::find($validated['user_id']);
            if (!$user || $user->usertype !== 'user') {
                toastr()->closeButton()->error('Invalid user selection. Only regular users can be added as tenants.');
                return redirect()->back()->withInput();
            }

            $existingTenant = Tenant::where('user_id', $validated['user_id'])->first();
            if ($existingTenant) {
                toastr()->closeButton()->error('This user is already registered as a tenant.');
                return redirect()->back()->withInput();
            }

            $existingHouse = Tenant::where('house_id', $validated['house_id'])->first();
            if ($existingHouse) {
                toastr()->closeButton()->error('This house is already occupied by another tenant.');
                return redirect()->back()->withInput();
            }

            $tenant = new Tenant();
            $tenant->user_id = $validated['user_id'];
            $tenant->house_id = $validated['house_id'];
            $tenant->landlord_id = $validated['landlord_id'] ?? null;
            $tenant->national_id = $validated['national_id'] ?? null;
            $tenant->rent = $validated['rent'];
            $tenant->utilities = $validated['utilities'] ?? 0;
            $tenant->security_deposit = $validated['security_deposit'] ?? 0;
            $tenant->lease_start_date = $validated['lease_start_date'] ?? null;
            $tenant->lease_end_date = $validated['lease_end_date'] ?? null;
            $tenant->emergency_contact_name = $validated['emergency_contact_name'] ?? null;
            $tenant->emergency_contact_phone = $validated['emergency_contact_phone'] ?? null;
            $tenant->save();

            if ($user->email) {
                $loginUrl = url('/login');
                Mail::raw("Hello {$user->name},\n\nYour tenancy record has been created successfully.\nYou can access your account here: {$loginUrl}", function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Welcome to the Tenant System');
                });
            }

            toastr()->closeButton()->success('Tenant added successfully and email sent.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function view_tenant(){
        // ✅ FIX: Use pagination and only load needed columns
        $tenants = Tenant::with([
            'user:id,name,email,phone',
            'house:id,title,price'
        ])->paginate(15);

        return view('admin.tenant.view_tenant', compact('tenants'));
    }

    public function delete_tenant($id){
        $tenants = Tenant::find($id);
        $tenants->delete();

        toastr()->closeButton()->success('Tenant deleted successfully.');
        return redirect()->back();
    }

    public function edit_tenant($id)
    {
        $tenants = Tenant::findOrFail($id);
        $users = User::select('id', 'name', 'email')->get();
        $houses = House::select('id', 'title', 'price')->get();
        $landlords = User::where('usertype', 'landlord')
                        ->select('id', 'name')
                        ->get();

        return view('admin.tenant.edit_tenant', compact('tenants', 'users', 'houses', 'landlords'));
    }

    public function update_tenant(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'house_id' => 'required|exists:houses,id',
            'landlord_id' => 'nullable|exists:users,id',
            'national_id' => 'nullable|string|max:255',
            'rent' => 'required|numeric|min:0',
            'utilities' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'lease_start_date' => 'nullable|date',
            'lease_end_date' => 'nullable|date|after_or_equal:lease_start_date',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        try {
            $tenants = Tenant::findOrFail($id);

            $tenants->user_id = $validated['user_id'];
            $tenants->house_id = $validated['house_id'];
            $tenants->landlord_id = $validated['landlord_id'] ?? null;
            $tenants->national_id = $validated['national_id'] ?? null;
            $tenants->rent = $validated['rent'];
            $tenants->utilities = $validated['utilities'] ?? 0;
            $tenants->security_deposit = $validated['security_deposit'] ?? 0;
            $tenants->lease_start_date = $validated['lease_start_date'] ?? null;
            $tenants->lease_end_date = $validated['lease_end_date'] ?? null;
            $tenants->emergency_contact_name = $validated['emergency_contact_name'] ?? null;
            $tenants->emergency_contact_phone = $validated['emergency_contact_phone'] ?? null;

            $tenants->save();

            toastr()->closeButton()->success('Tenant updated successfully.');
            return redirect('/view_tenant');

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error updating tenant: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // Landlords
    public function add_landlord(){
        $users = User::where('usertype', 'user')
                    ->whereNotIn('id', Landlord::pluck('user_id'))
                    ->select('id', 'name', 'email') // ✅ FIX
                    ->get();

        return view('admin.landlord.add_landlord', compact('users'));
    }

    public function upload_landlord(Request $request){
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'national_id' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
        ]);

        try {
            $existingLandlord = Landlord::where('user_id', $validated['user_id'])->first();
            if ($existingLandlord) {
                toastr()->closeButton()->error('This user is already registered as a landlord.');
                return redirect()->back()->withInput();
            }

            $landlord = new Landlord();
            $landlord->user_id = $validated['user_id'];
            $landlord->national_id = $validated['national_id'] ?? null;
            $landlord->company_name = $validated['company_name'] ?? null;
            $landlord->save();

            $user = User::find($validated['user_id']);
            $user->usertype = 'landlord';
            $user->save();

            if ($user->email) {
                $loginUrl = url('/login');
                Mail::raw("Hello {$user->name},\n\nYou have been registered as a landlord in our system.\nYou can access your account here: {$loginUrl}", function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Landlord Account Created');
                });
            }

            toastr()->closeButton()->success('Landlord added successfully and email sent.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function view_landlord(){
        // ✅ CRITICAL FIX: Use pagination and withCount instead of loading all houses
        $landlords = Landlord::with('user:id,name,email,phone')
                            ->withCount('houses') // Just count, don't load all houses
                            ->paginate(15);

        return view('admin.landlord.view_landlord', compact('landlords'));
    }

    public function delete_landlord($id){
        try {
            $landlord = Landlord::findOrFail($id);
            $user = $landlord->user;
            $landlord->delete();

            if ($user) {
                $user->usertype = 'user';
                $user->save();
            }

            toastr()->closeButton()->success('Landlord deleted successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error deleting landlord: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit_landlord($id)
    {
        $landlord = Landlord::with('user')->findOrFail($id);
        $excludedUserIds = Landlord::where('id', '!=', $id)->pluck('user_id');

        $users = User::whereNotIn('id', $excludedUserIds)
                    ->orWhere('id', $landlord->user_id)
                    ->select('id', 'name', 'email')
                    ->get();

        return view('admin.landlord.edit_landlord', compact('landlord', 'users'));
    }

    public function update_landlord(Request $request, $id){
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'national_id' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
        ]);

        try {
            $landlord = Landlord::findOrFail($id);

            $existingLandlord = Landlord::where('user_id', $validated['user_id'])
                                    ->where('id', '!=', $id)
                                    ->first();
            if ($existingLandlord) {
                toastr()->closeButton()->error('This user is already registered as another landlord.');
                return redirect()->back()->withInput();
            }

            $oldUserId = $landlord->user_id;

            $landlord->user_id = $validated['user_id'];
            $landlord->national_id = $validated['national_id'] ?? null;
            $landlord->company_name = $validated['company_name'] ?? null;
            $landlord->save();

            if ($oldUserId != $validated['user_id']) {
                $oldUser = User::find($oldUserId);
                if ($oldUser) {
                    $oldUser->usertype = 'user';
                    $oldUser->save();
                }

                $newUser = User::find($validated['user_id']);
                if ($newUser) {
                    $newUser->usertype = 'landlord';
                    $newUser->save();
                }
            }

            toastr()->closeButton()->success('Landlord updated successfully.');
            return redirect('/view_landlord');

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error updating landlord: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
