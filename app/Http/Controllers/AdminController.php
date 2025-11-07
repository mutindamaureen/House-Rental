<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Contract;
use App\Models\House;
use App\Models\Landlord;
use App\Models\MaintenanceRequest;
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

    // =========================
    // Maintenance Requests
    // =========================

    public function view_maintenancerequest()
    {
        $data = MaintenanceRequest::with([
            'tenant:id,name,email',
            'landlord:id,name,email'
        ])->latest()->paginate(15);

        return view('admin.maintenance.view_maintenance', compact('data'));
    }

    public function add_maintenancerequest()
    {
        $tenants = User::where('usertype', 'tenant')->select('id', 'name', 'email')->get();
        $landlords = User::where('usertype', 'landlord')->select('id', 'name', 'email')->get();
        $houses = House::select('id', 'title')->get();

        return view('admin.maintenance.add_maintenance', compact('tenants', 'landlords', 'houses'));
    }

    public function upload_maintenancerequest(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'landlord_id' => 'nullable|exists:users,id',
            'house_name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
        ]);

        try {
            $maintenance = new MaintenanceRequest();
            $maintenance->tenant_id = $validated['tenant_id'];
            $maintenance->landlord_id = $validated['landlord_id'] ?? null;
            $maintenance->house_name = $validated['house_name'];
            $maintenance->subject = $validated['subject'];
            $maintenance->description = $validated['description'];
            $maintenance->status = $validated['status'] ?? 'pending';
            $maintenance->save();

            // Optional: Email landlord
            if ($maintenance->landlord_id) {
                $landlord = User::find($maintenance->landlord_id);
                if ($landlord && $landlord->email) {
                    Mail::raw(
                        "Hello {$landlord->name},\n\nA new maintenance request has been submitted.\n\nSubject: {$maintenance->subject}\nHouse: {$maintenance->house_name}\n\nPlease log in to view details.",
                        function ($message) use ($landlord) {
                            $message->to($landlord->email)->subject('New Maintenance Request');
                        }
                    );
                }
            }

            toastr()->closeButton()->success('Maintenance request added successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit_maintenancerequest($id)
    {
        $maintenance = MaintenanceRequest::with(['tenant', 'landlord'])->findOrFail($id);
        $tenants = User::where('usertype', 'tenant')->select('id', 'name', 'email')->get();
        $landlords = User::where('usertype', 'landlord')->select('id', 'name', 'email')->get();
        $houses = House::select('id', 'title')->get();

        return view('admin.maintenance.edit_maintenance', compact('maintenance', 'tenants', 'landlords', 'houses'));
    }

    public function update_maintenancerequest(Request $request, $id)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'landlord_id' => 'nullable|exists:users,id',
            'house_name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        try {
            $maintenance = MaintenanceRequest::findOrFail($id);
            $maintenance->tenant_id = $validated['tenant_id'];
            $maintenance->landlord_id = $validated['landlord_id'] ?? null;
            $maintenance->house_name = $validated['house_name'];
            $maintenance->subject = $validated['subject'];
            $maintenance->description = $validated['description'];
            $maintenance->status = $validated['status'];
            $maintenance->save();

            toastr()->closeButton()->success('Maintenance request updated successfully.');
            return redirect('/view_maintenancerequest');

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete_maintenancerequest($id)
    {
        try {
            $maintenance = MaintenanceRequest::findOrFail($id);
            $maintenance->delete();

            toastr()->closeButton()->success('Maintenance request deleted successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error deleting maintenance request: ' . $e->getMessage());
            return redirect()->back();
        }
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


    public function view_tenant(){
        // ✅ FIX: Use pagination and only load needed columns
        $tenants = Tenant::with([
            'user:id,name,email,phone',
            'house:id,title,price'
        ])->paginate(15);

        return view('admin.tenant.view_tenant', compact('tenants'));
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

    public function add_tenant(){
        $users = User::where('usertype', 'user')
                    ->whereNotIn('id', Tenant::pluck('user_id'))
                    ->select('id', 'name', 'email')
                    ->get();

        $houses = House::whereNotIn('id', Tenant::pluck('house_id'))
                    ->select('id', 'title', 'price')
                    ->get();

        $landlords = User::where('usertype', 'landlord')
                        ->select('id', 'name')
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

            // ✅ CHANGE USERTYPE TO TENANT
            $user->usertype = 'tenant';
            $user->save();

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

    public function delete_tenant($id){
        try {
            $tenant = Tenant::findOrFail($id);
            $user = $tenant->user;

            $tenant->delete();

            // ✅ REVERT USERTYPE BACK TO USER
            if ($user) {
                $user->usertype = 'user';
                $user->save();
            }

            toastr()->closeButton()->success('Tenant deleted successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error deleting tenant: ' . $e->getMessage());
            return redirect()->back();
        }
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
            $tenant = Tenant::findOrFail($id);
            $oldUserId = $tenant->user_id;

            // Check if new user is already a tenant (if user is being changed)
            if ($oldUserId != $validated['user_id']) {
                $existingTenant = Tenant::where('user_id', $validated['user_id'])
                                        ->where('id', '!=', $id)
                                        ->first();
                if ($existingTenant) {
                    toastr()->closeButton()->error('This user is already registered as another tenant.');
                    return redirect()->back()->withInput();
                }
            }

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

            // ✅ HANDLE USERTYPE CHANGES IF USER WAS CHANGED
            if ($oldUserId != $validated['user_id']) {
                // Revert old user back to 'user'
                $oldUser = User::find($oldUserId);
                if ($oldUser) {
                    $oldUser->usertype = 'user';
                    $oldUser->save();
                }

                // Set new user to 'tenant'
                $newUser = User::find($validated['user_id']);
                if ($newUser) {
                    $newUser->usertype = 'tenant';
                    $newUser->save();
                }
            }

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

    // =========================
    // Contract Management
    // =========================

    public function view_contract()
    {
        $data = Contract::with([
            'landlord:id,name,email',
            'tenant:id,name,email',
            'house:id,title,location'
        ])
        ->latest()
        ->paginate(15);

        return view('admin.contract.view_contract', compact('data'));
    }

    public function add_contract()
    {
        // Get landlords
        $landlords = User::where('usertype', 'landlord')
                        ->select('id', 'name', 'email')
                        ->get();

        // Get tenants
        $tenants = User::where('usertype', 'tenant')
                      ->select('id', 'name', 'email')
                      ->get();

        // Get houses
        $houses = House::select('id', 'title', 'location', 'price')->get();

        return view('admin.contract.add_contract', compact('landlords', 'tenants', 'houses'));
    }

    public function upload_contract(Request $request)
    {
        $validated = $request->validate([
            'landlord_id' => 'required|exists:users,id',
            'tenant_id' => 'required|exists:users,id',
            'house_id' => 'required|exists:houses,id',
            'contract_pdf' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'status' => 'nullable|in:pending,signed',
        ]);

        try {
            // Check if landlord is actually a landlord
            $landlord = User::find($validated['landlord_id']);
            if ($landlord->usertype !== 'landlord') {
                toastr()->closeButton()->error('Selected user is not a landlord.');
                return redirect()->back()->withInput();
            }

            // Check if tenant is actually a tenant
            $tenant = User::find($validated['tenant_id']);
            if ($tenant->usertype !== 'tenant') {
                toastr()->closeButton()->error('Selected user is not a tenant.');
                return redirect()->back()->withInput();
            }

            // Check for existing contract
            $existingContract = Contract::where('tenant_id', $validated['tenant_id'])
                                       ->where('house_id', $validated['house_id'])
                                       ->where('status', 'pending')
                                       ->first();

            if ($existingContract) {
                toastr()->closeButton()->error('An active contract already exists for this tenant and house.');
                return redirect()->back()->withInput();
            }

            $contract = new Contract();
            $contract->landlord_id = $validated['landlord_id'];
            $contract->tenant_id = $validated['tenant_id'];
            $contract->house_id = $validated['house_id'];
            $contract->status = $validated['status'] ?? 'pending';

            // Handle PDF upload
            if ($request->hasFile('contract_pdf')) {
                $file = $request->file('contract_pdf');
                $filename = time() . '_' . uniqid() . '.pdf';
                $file->move(public_path('contracts'), $filename);
                $contract->contract_pdf = $filename;
            }

            $contract->save();

            // Send email notification to tenant
            if ($tenant->email) {
                $contractUrl = url('/tenant/contracts');
                Mail::raw(
                    "Hello {$tenant->name},\n\nA new contract has been created for you.\n\nHouse: {$contract->house->title}\nLocation: {$contract->house->location}\n\nPlease log in to review and sign the contract: {$contractUrl}",
                    function ($message) use ($tenant) {
                        $message->to($tenant->email)
                               ->subject('New Contract - Action Required');
                    }
                );
            }

            toastr()->closeButton()->success('Contract added successfully and notification sent to tenant.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit_contract($id)
    {
        $contract = Contract::with(['landlord', 'tenant', 'house'])->findOrFail($id);

        $landlords = User::where('usertype', 'landlord')
                        ->select('id', 'name', 'email')
                        ->get();

        $tenants = User::where('usertype', 'tenant')
                      ->select('id', 'name', 'email')
                      ->get();

        $houses = House::select('id', 'title', 'location', 'price')->get();

        return view('admin.contract.edit_contract', compact('contract', 'landlords', 'tenants', 'houses'));
    }

    public function update_contract(Request $request, $id)
    {
        $validated = $request->validate([
            'landlord_id' => 'required|exists:users,id',
            'tenant_id' => 'required|exists:users,id',
            'house_id' => 'required|exists:houses,id',
            'contract_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:pending,signed',
        ]);

        try {
            $contract = Contract::findOrFail($id);

            // Check if landlord is actually a landlord
            $landlord = User::find($validated['landlord_id']);
            if ($landlord->usertype !== 'landlord') {
                toastr()->closeButton()->error('Selected user is not a landlord.');
                return redirect()->back()->withInput();
            }

            // Check if tenant is actually a tenant
            $tenant = User::find($validated['tenant_id']);
            if ($tenant->usertype !== 'tenant') {
                toastr()->closeButton()->error('Selected user is not a tenant.');
                return redirect()->back()->withInput();
            }

            $contract->landlord_id = $validated['landlord_id'];
            $contract->tenant_id = $validated['tenant_id'];
            $contract->house_id = $validated['house_id'];
            $contract->status = $validated['status'];

            // Handle PDF upload if new file is provided
            if ($request->hasFile('contract_pdf')) {
                // Delete old PDF
                $old_pdf_path = public_path('contracts/' . $contract->contract_pdf);
                if (file_exists($old_pdf_path) && $contract->contract_pdf) {
                    unlink($old_pdf_path);
                }

                // Upload new PDF
                $file = $request->file('contract_pdf');
                $filename = time() . '_' . uniqid() . '.pdf';
                $file->move(public_path('contracts'), $filename);
                $contract->contract_pdf = $filename;
            }

            $contract->save();

            toastr()->closeButton()->success('Contract updated successfully.');
            return redirect('/view_contract');

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error updating contract: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete_contract($id)
    {
        try {
            $contract = Contract::findOrFail($id);

            // Delete PDF file
            $pdf_path = public_path('contracts/' . $contract->contract_pdf);
            if (file_exists($pdf_path)) {
                unlink($pdf_path);
            }

            // Delete signature file if exists
            if ($contract->tenant_signature) {
                $signature_path = public_path('signatures/' . $contract->tenant_signature);
                if (file_exists($signature_path)) {
                    unlink($signature_path);
                }
            }

            $contract->delete();

            toastr()->closeButton()->success('Contract deleted successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error deleting contract: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function view_contract_details($id)
    {
        $contract = Contract::with(['landlord', 'tenant', 'house'])->findOrFail($id);
        return view('admin.contract.view_details', compact('contract'));
    }

    public function download_contract($id)
    {
        $contract = Contract::findOrFail($id);
        $file_path = public_path('contracts/' . $contract->contract_pdf);

        if (!file_exists($file_path)) {
            toastr()->closeButton()->error('Contract file not found.');
            return redirect()->back();
        }

        return response()->download($file_path);
    }


}
