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
        // Get counts
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

        // ✅ Fix: pad months (1–12) so missing months show as 0
        $tenantData = array_replace(array_fill(1, 12, 0), $monthlyTenants);
        $houseData = array_replace(array_fill(1, 12, 0), $monthlyHouses);

        $housesByCategory = House::selectRaw('category, SUM(quantity) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Get recent tenants
        $recentTenants = Tenant::with('user', 'house')
            ->latest()
            ->take(5)
            ->get();

        // Get top landlords by number of houses
        $topLandlords = Landlord::withCount('houses')
            ->with('user')
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
            'tenantData',   // ✅ use these instead of monthlyTenants
            'houseData',    // ✅ use these instead of monthlyHouses
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
        $data = Category::all();

        return view('admin.category.category', compact('data'));
    }

    public function add_category(Request $request){
        $category = new Category;

        $category -> category_name = $request->category;

        $category->save();

        toastr()->closeButton()->success('Category added successfully.');

        return redirect()->back();
    }

    public function delete_category($id){
        $data=Category::find($id);

        $data->delete();

        toastr()->closeButton()->success('Category deleted successfully.');

        return redirect()->back();
    }

    public function edit_category($id){

        $data=Category::find($id);

        return view('admin.category.edit_category', compact('data'));
    }

    public function update_category(Request $request, $id){
        $data = Category::find($id);

        $data -> category_name = $request->category;

        $data->save();

        toastr()->closeButton()->success('Category updated successfully.');


        return redirect ('/view_category');
    }

    // House
    public function add_house(){
        $category = Category::all();
        $landlords = Landlord::with('user')->get();

        // $landlords = Landlord::all(); // Fetch all landlords
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
        $data->landlord_id = $request->landlord_id; // New field
        $data->status = $request->status ?? 'available'; // New field with default

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
        $house = House::with('landlord')->paginate(5); // Eager load landlord relationship
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
        $landlords = Landlord::all(); // Fetch all landlords
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
        $house->landlord_id = $request->landlord_id; // New field
        $house->status = $request->status ?? $house->status; // New field, keep existing if not provided

        $image = $request->image;
        if($image){
            // Delete old image if exists
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
            ->with('landlord') // Eager load landlord relationship
            ->paginate(3);
        return view('admin.house.view_house', compact('house'));
    }

    // Users
    public function view_user(){
        $data = User::all();

        return view('admin.user.users', compact('data'));
    }

    public function add_user(){

        $user = User::all();

        return view('admin.user.add_user', compact('user'));
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

        // Create user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->usertype = $request->usertype;
        $user->password = Hash::make($request->password);
        $user->save();

        // Send welcome email
        $loginUrl = url('/login');
        Mail::raw("Hello {$user->name},\n\nYour account has been created successfully.\nYou can access the system here: {$loginUrl}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Welcome to the System');
        });
        toastr()->closeButton()->success('User added successfully and email sent.');

        return redirect()->back();
        }

    public function delete_user($id){
        $data=User::find($id);

        $data->delete();

        toastr()->closeButton()->success('User deleted successfully.');

        return redirect()->back();
    }

    public function edit_user($id){

        $data=User::find($id);

        return view('admin.user.edit_user', compact('data'));
    }

    public function update_user(Request $request, $id){
        $data = User::find($id);

        $data -> name = $request->name;
        $data -> email = $request->email;
        $data -> phone = $request->phone;
        $data -> address = $request->address;
        $data -> usertype = $request->usertype;
        // $data -> password = $request->password;
        if ($request->filled('password')) {
            $data->password = Hash::make($request->password);
        }

        $data->save();

        toastr()->closeButton()->success('User updated successfully.');


        return redirect ('/view_user');
    }

    public function add_tenant(){
        // Only get users with usertype 'user' who are not already tenants
        $users = User::where('usertype', 'user')
                    ->whereNotIn('id', Tenant::pluck('user_id'))
                    ->get();

        // Only get houses that are not already assigned to other tenants
        $houses = House::whereNotIn('id', Tenant::pluck('house_id'))->get();

        $landlords = User::where('usertype', 'landlord')->get();

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
            // Additional check: Verify the selected user is of type 'user'
            $user = User::find($validated['user_id']);
            if (!$user || $user->usertype !== 'user') {
                toastr()->closeButton()->error('Invalid user selection. Only regular users can be added as tenants.');
                return redirect()->back()->withInput();
            }

            // Check if user is already a tenant
            $existingTenant = Tenant::where('user_id', $validated['user_id'])->first();
            if ($existingTenant) {
                toastr()->closeButton()->error('This user is already registered as a tenant.');
                return redirect()->back()->withInput();
            }

            // Check if the house is already occupied
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
        $tenants = Tenant::all();

        return view('admin.tenant.view_tenant', compact('tenants'));
    }


    public function delete_tenant($id){
        $tenants=Tenant::find($id);

        $tenants->delete();

        toastr()->closeButton()->success('Tenant deleted successfully.');

        return redirect()->back();
    }

    public function edit_tenant($id)
    {
        $tenants = Tenant::findOrFail($id);
        $users = User::all();
        $houses = House::all();
        $landlords = User::where('usertype', 'landlord')->get();

        return view('admin.tenant.edit_tenant', compact('tenants', 'users', 'houses', 'landlords'));
    }

    public function update_tenant(Request $request, $id)
    {
        // Validate input
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

            // Update tenant details
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
        // Get users who are not already landlords
        $users = User::where('usertype', 'user')
                    ->whereNotIn('id', Landlord::pluck('user_id'))
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
            // Check if user is already a landlord
            $existingLandlord = Landlord::where('user_id', $validated['user_id'])->first();
            if ($existingLandlord) {
                toastr()->closeButton()->error('This user is already registered as a landlord.');
                return redirect()->back()->withInput();
            }

            // Create landlord record
            $landlord = new Landlord();
            $landlord->user_id = $validated['user_id'];
            $landlord->national_id = $validated['national_id'] ?? null;
            $landlord->company_name = $validated['company_name'] ?? null;
            $landlord->save();

            // Update user type to landlord
            $user = User::find($validated['user_id']);
            $user->usertype = 'landlord';
            $user->save();

            // Send email notification
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

    // public function view_landlord(){
    //     $landlords = Landlord::with('user')->get();

    //     return view('admin.landlord.view_landlord', compact('landlords'));
    // }
    public function view_landlord(){
        // Eager load both user and houses relationships
        $landlords = Landlord::with(['user', 'houses'])->get();

        return view('admin.landlord.view_landlord', compact('landlords'));
    }
    public function delete_landlord($id){
        try {
            $landlord = Landlord::findOrFail($id);

            // Get the user before deleting landlord record
            $user = $landlord->user;

            // Delete the landlord record
            $landlord->delete();

            // Optionally revert user type back to 'user'
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
        // Fetch landlord with associated user
        $landlord = Landlord::with('user')->findOrFail($id);

        // Get IDs of users who are already landlords, excluding the current landlord
        $excludedUserIds = Landlord::where('id', '!=', $id)->pluck('user_id');

        // Get all users who are not landlords, or who are the current landlord's user
        $users = User::whereNotIn('id', $excludedUserIds)
                    ->orWhere('id', $landlord->user_id)
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

            // Check if the new user_id is already used by another landlord
            $existingLandlord = Landlord::where('user_id', $validated['user_id'])
                                    ->where('id', '!=', $id)
                                    ->first();
            if ($existingLandlord) {
                toastr()->closeButton()->error('This user is already registered as another landlord.');
                return redirect()->back()->withInput();
            }

            // Store old user_id to revert usertype if changed
            $oldUserId = $landlord->user_id;

            // Update landlord record
            $landlord->user_id = $validated['user_id'];
            $landlord->national_id = $validated['national_id'] ?? null;
            $landlord->company_name = $validated['company_name'] ?? null;
            $landlord->save();

            // If user changed, update usertypes
            if ($oldUserId != $validated['user_id']) {
                // Revert old user back to 'user'
                $oldUser = User::find($oldUserId);
                if ($oldUser) {
                    $oldUser->usertype = 'user';
                    $oldUser->save();
                }

                // Set new user to 'landlord'
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
