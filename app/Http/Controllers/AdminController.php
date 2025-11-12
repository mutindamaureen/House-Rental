<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Contract;
use App\Models\House;
use App\Models\Invoices;
use App\Models\Landlord;
use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        $tenantData = array_replace(array_fill(1, 12, 0), $monthlyTenants);
        $houseData = array_replace(array_fill(1, 12, 0), $monthlyHouses);

        $housesByCategory = House::selectRaw('category, SUM(quantity) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $recentTenants = Tenant::with([
            'user:id,name,email',
            'house:id,title,price'
        ])
            ->latest()
            ->take(5)
            ->get();

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
        $data = Category::paginate(15);
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

        // Get tenant relationships for auto-population
        $tenantData = Tenant::with(['house:id,title,landlord_id', 'landlord:id'])
            ->get()
            ->mapWithKeys(function ($tenant) {
                return [
                    $tenant->user_id => [
                        'house_id' => $tenant->house_id,
                        'house_name' => $tenant->house->title ?? null,
                        'landlord_id' => $tenant->landlord_id,
                    ]
                ];
            });

        return view('admin.maintenance.add_maintenance', compact('tenants', 'landlords', 'houses', 'tenantData'));
    }

    public function get_tenant_details($id)
    {
        try {
            $tenant = Tenant::with(['house:id,title', 'landlord:id'])
                ->where('user_id', $id)
                ->first();

            if (!$tenant) {
                return response()->json(['success' => false, 'message' => 'Tenant not found']);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'house_id' => $tenant->house_id,
                    'house_name' => $tenant->house->title ?? '',
                    'landlord_id' => $tenant->landlord_id,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
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

            // Email notification
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

                // Generate WhatsApp link
                if ($landlord && $landlord->phone) {
                    $landlordPhone = preg_replace('/[^0-9]/', '', $landlord->phone);
                    if (substr($landlordPhone, 0, 1) === '0') {
                        $landlordPhone = '254' . substr($landlordPhone, 1);
                    } elseif (substr($landlordPhone, 0, 3) !== '254') {
                        $landlordPhone = '254' . $landlordPhone;
                    }

                    $tenant = User::find($maintenance->tenant_id);
                    $message = urlencode(
                        "🔧 *New Maintenance Request*\n\n" .
                        "Tenant: " . $tenant->name . "\n" .
                        "House: " . $maintenance->house_name . "\n\n" .
                        "Issue: " . $maintenance->subject . "\n" .
                        "Description: " . $maintenance->description . "\n\n" .
                        "Status: " . ucfirst($maintenance->status)
                    );

                    $whatsappUrl = "https://wa.me/{$landlordPhone}?text={$message}";

                    toastr()->closeButton()->success('Maintenance request added successfully.');
                    return redirect()->back()->with('whatsapp_url', $whatsappUrl);
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
        $data = User::paginate(15);
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
                    ->select('id', 'name', 'email')
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
        $landlords = Landlord::with('user:id,name,email,phone')
                            ->withCount('houses')
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
        // Get all landlords
        $landlords = User::where('usertype', 'landlord')
                        ->select('id', 'name', 'email')
                        ->get();

        // Don't load tenants and houses initially - they'll be loaded via AJAX
        return view('admin.contract.add_contract', compact('landlords'));
    }

    // Add these new methods to your controller

    public function get_tenants_by_landlord($landlord_id)
    {
        try {
            // Get tenants that belong to this landlord based on the tenants table landlord_id column
            $tenants = Tenant::with('user:id,name,email')
                            ->where('landlord_id', $landlord_id)
                            ->get()
                            ->map(function($tenant) {
                                return [
                                    'id' => $tenant->user_id,
                                    'name' => $tenant->user->name,
                                    'email' => $tenant->user->email
                                ];
                            });

            return response()->json($tenants);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_house_by_tenant($tenant_id)
    {
        try {
            // Find tenant record by user_id
            $tenant = Tenant::with('house:id,title,location,price')
                        ->where('user_id', $tenant_id)
                        ->first();

            if ($tenant && $tenant->house) {
                return response()->json([
                    'house' => $tenant->house
                ]);
            }

            return response()->json(['house' => null]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function upload_contract(Request $request)
    {
        $validated = $request->validate([
            'landlord_id' => 'required|exists:users,id',
            'tenant_id' => 'required|exists:users,id',
            'house_id' => 'required|exists:houses,id',
            'contract_pdf' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'status' => 'nullable|in:pending,signed, terminated'
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
            'status' => 'required|in:pending,signed,'
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

    public function initiateTermination($id)
    {
        try {
            $contract = Contract::with(['landlord', 'tenant', 'house'])->findOrFail($id);

            // Check if contract is already signed or terminated
            if ($contract->status !== 'signed') {
                toastr()->closeButton()->error('Only signed contracts can be terminated.');
                return redirect()->back();
            }

            if ($contract->termination_status !== null) {
                toastr()->closeButton()->error('This contract is already in termination process.');
                return redirect()->back();
            }

            // Update contract to termination pending
            $contract->termination_status = 'pending';
            $contract->termination_initiated_at = now();
            $contract->termination_initiated_by = 'admin';
            $contract->save();

            // Send notifications to both parties
            $tenant = User::find($contract->tenant_id);
            $landlord = User::find($contract->landlord_id);

            if ($tenant && $tenant->email) {
                Mail::raw(
                    "Hello {$tenant->name},\n\nThe admin has initiated termination for your contract at {$contract->house->title}.\n\nPlease log in to review and sign the termination agreement.",
                    function ($message) use ($tenant) {
                        $message->to($tenant->email)->subject('Contract Termination Initiated');
                    }
                );
            }

            if ($landlord && $landlord->email) {
                Mail::raw(
                    "Hello {$landlord->name},\n\nThe admin has initiated termination for the contract at {$contract->house->title}.\n\nPlease log in to review and sign the termination agreement.",
                    function ($message) use ($landlord) {
                        $message->to($landlord->email)->subject('Contract Termination Initiated');
                    }
                );
            }

            toastr()->closeButton()->success('Contract termination initiated successfully. Both parties will be notified.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function viewTerminationDetails($id)
    {
        $contract = Contract::with(['landlord', 'tenant', 'house'])->findOrFail($id);

        if (!in_array($contract->termination_status, ['pending', 'partial', 'completed'])) {
            toastr()->closeButton()->error('This contract is not in termination process.');
            return redirect()->back();
        }

        return view('admin.contract.termination_details', compact('contract'));
    }

    public function cancelTermination($id)
    {
        try {
            $contract = Contract::findOrFail($id);

            if ($contract->termination_status === 'completed') {
                toastr()->closeButton()->error('Cannot cancel a completed termination.');
                return redirect()->back();
            }

            // Reset termination fields
            $contract->termination_status = null;
            $contract->termination_initiated_at = null;
            $contract->termination_initiated_by = null;
            $contract->landlord_termination_signature = null;
            $contract->tenant_termination_signature = null;
            $contract->landlord_signed_termination_at = null;
            $contract->tenant_signed_termination_at = null;
            $contract->save();

            toastr()->closeButton()->success('Contract termination cancelled successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function terminated_contracts()
    {
        $data = Contract::with([
            'landlord:id,name,email',
            'tenant:id,name,email',
            'house:id,title,location'
        ])
        ->where('status', 'terminated')
        ->latest('terminated_at')
        ->paginate(15);

        return view('admin.contract.terminated_contracts', compact('data'));
    }


    // ================================
    // List All Payments
    // ================================
    public function view_payments()
    {
        $payments = Payments::with([
            'tenant.user:id,name,email',
            'invoice'
        ])
        ->latest()
        ->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }


    // ================================
    // Show Add Payment Form
    // ================================
    public function add_payment()
    {
        $tenants = Tenant::with('user:id,name,email')->get();

        // Load unpaid invoices only
        $invoices = DB::table('invoices')
            ->where('status', 'unpaid')
            ->select('id', 'reference', 'amount', 'tenant_id')
            ->get();

        return view('admin.payments.create', compact('tenants', 'invoices'));
    }


    // ================================
    // Store Payment (Admin Manual Entry)
    // ================================
    public function upload_payment(Request $request)
    {
            $validated = $request->validate([
                'invoice_id' => 'nullable|exists:invoices,id',
                'tenant_id'  => 'nullable|exists:tenants,id',
                'amount'     => 'required|numeric|min:0.01',
                'currency'   => 'nullable|string|size:3',
                'payment_method' => ['required', Rule::in(['card','mpesa','paybill','till','bank_transfer','cash'])],
                'status'     => ['required', Rule::in(['pending','initiated','succeeded','failed','refunded','cancelled'])],
                'gateway'    => 'nullable|string',
                'gateway_transaction_id' => 'nullable|string',
                'fees_amount'=> 'nullable|numeric|min:0',
                'notes'      => 'nullable|string',
            ]);

        try {
            $payment = Payments::create([
                'invoice_id' => $validated['invoice_id'] ?? null,
                'tenant_id' => $validated['tenant_id'] ?? null,
                'amount' => $validated['amount'],
                'currency' => $validated['currency'] ?? 'KES',
                'payment_method' => $validated['payment_method'],
                'status' => $validated['status'],

                'gateway' => $validated['gateway'] ?? null,
                'gateway_transaction_id' => $validated['gateway_transaction_id'] ?? null,

                // Use invoice ID as merchant reference OR manual reference
                'merchant_reference' => $validated['invoice_id']
                    ? ('inv-' . $validated['invoice_id'])
                    : ('manual-' . Str::uuid()),

                'fees_amount' => $validated['fees_amount'] ?? 0,
                'net_amount' => ($validated['amount'] ?? 0) - ($validated['fees_amount'] ?? 0),

                'paid_at' => $validated['status'] === 'succeeded' ? now() : null,
                'notes' => $validated['notes'] ?? null,

                // Prevent duplicate payment submissions
                'idempotency_key' => Str::uuid(),
            ]);

            // Mark invoice as paid if this was a paid invoice
            if ($payment->invoice_id && $payment->status === 'succeeded') {
                DB::table('invoices')->where('id', $payment->invoice_id)->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }

            toastr()->closeButton()->success('Payment recorded successfully.');
            // return redirect()->route('');
            return redirect()->back();

        } catch (\Exception $e) {

            toastr()->closeButton()->error('Error saving payment: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    // ================================
    // View a Single Payment
    // ================================
    public function view_payment($id)
    {
        $payment = Payments::with([
            'tenant.user:id,name,email',
            'invoice'
        ])->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }


    // ================================
    // Delete Payment
    // ================================
    public function delete_payment($id)
    {
        try {
            $payment = Payments::findOrFail($id);

            // Optional: reverse invoice status if needed
            if ($payment->invoice_id && $payment->status === 'succeeded') {
                DB::table('invoices')
                    ->where('id', $payment->invoice_id)
                    ->update([
                        'status' => 'unpaid',
                        'paid_at' => null,
                    ]);
            }

            $payment->delete();

            toastr()->closeButton()->success('Payment deleted successfully.');
            return redirect()->back();

        } catch (\Exception $e) {

            toastr()->closeButton()->error('Error deleting payment: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // ================================
    // List All Invoices
    // ================================
    public function view_invoice()
    {
        $invoices = Invoices::with([
            'tenant.user:id,name,email',
            'house:id,title,location'
        ])
        ->latest()
        ->paginate(15);

        return view('admin.invoice.view_invoice', compact('invoices'));
    }

    // ================================
    // Show Add Invoice Form
    // ================================
    public function add_invoice()
    {
        // Get all tenants with their user info
        $tenants = Tenant::with(['user:id,name,email', 'house:id,title'])
            ->get();

        // Get all houses
        $houses = House::select('id', 'title', 'location', 'price')->get();

        return view('admin.invoice.add_invoice', compact('tenants', 'houses'));
    }

    // ================================
    // Store New Invoice
    // ================================
    public function upload_invoice(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'house_id' => 'required|exists:houses,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'description' => 'required|string|max:500',
            'due_date' => 'required|date|after_or_equal:today',
            'issued_date' => 'nullable|date',
            'status' => 'nullable|in:unpaid,paid,overdue,cancelled',
        ]);

        try {
            $invoice = new Invoices();
            $invoice->reference = Invoices::generateReference('INV');
            $invoice->tenant_id = $validated['tenant_id'];
            $invoice->house_id = $validated['house_id'];
            $invoice->amount = $validated['amount'];
            $invoice->currency = $validated['currency'] ?? 'KES';
            $invoice->description = $validated['description'];
            $invoice->due_date = $validated['due_date'];
            $invoice->issued_date = $validated['issued_date'] ?? now();
            $invoice->status = $validated['status'] ?? 'unpaid';
            $invoice->paid_amount = 0;
            $invoice->save();

            // Send email notification to tenant
            $tenant = Tenant::with('user')->find($validated['tenant_id']);
            if ($tenant && $tenant->user && $tenant->user->email) {
                $invoiceUrl = url('/tenant/invoices');
                Mail::raw(
                    "Hello {$tenant->user->name},\n\n" .
                    "A new invoice has been generated for you.\n\n" .
                    "Invoice #: {$invoice->reference}\n" .
                    "Amount: {$invoice->currency} {$invoice->amount}\n" .
                    "Due Date: {$invoice->due_date->format('d M Y')}\n" .
                    "Description: {$invoice->description}\n\n" .
                    "Please log in to view and pay: {$invoiceUrl}",
                    function ($message) use ($tenant) {
                        $message->to($tenant->user->email)
                            ->subject('New Invoice Generated');
                    }
                );
            }

            toastr()->closeButton()->success('Invoice created successfully and notification sent.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error creating invoice: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // ================================
    // Show Edit Invoice Form
    // ================================
    public function edit_invoice($id)
    {
        $invoice = Invoices::with(['tenant.user', 'house'])->findOrFail($id);

        $tenants = Tenant::with(['user:id,name,email', 'house:id,title'])->get();
        $houses = House::select('id', 'title', 'location', 'price')->get();

        return view('admin.invoice.edit_invoice', compact('invoice', 'tenants', 'houses'));
    }

    // ================================
    // Update Invoice
    // ================================
    public function update_invoice(Request $request, $id)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'house_id' => 'required|exists:houses,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'description' => 'required|string|max:500',
            'due_date' => 'required|date',
            'issued_date' => 'nullable|date',
            'status' => 'required|in:unpaid,paid,overdue,cancelled',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $invoice = Invoices::findOrFail($id);

            // Don't allow editing if invoice is paid and you're changing critical fields
            if ($invoice->status === 'paid' && $validated['status'] !== 'paid') {
                toastr()->closeButton()->warning('Warning: Changing status of a paid invoice.');
            }

            $invoice->tenant_id = $validated['tenant_id'];
            $invoice->house_id = $validated['house_id'];
            $invoice->amount = $validated['amount'];
            $invoice->currency = $validated['currency'] ?? 'KES';
            $invoice->description = $validated['description'];
            $invoice->due_date = $validated['due_date'];
            $invoice->issued_date = $validated['issued_date'] ?? $invoice->issued_date;
            $invoice->status = $validated['status'];
            $invoice->paid_amount = $validated['paid_amount'] ?? $invoice->paid_amount;

            // Auto-update status based on paid amount
            if ($invoice->paid_amount >= $invoice->amount && $invoice->status !== 'paid') {
                $invoice->status = 'paid';
            }

            $invoice->save();

            toastr()->closeButton()->success('Invoice updated successfully.');
            return redirect('/view_invoice');

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error updating invoice: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // ================================
    // Delete Invoice
    // ================================
    public function delete_invoice($id)
    {
        try {
            $invoice = Invoices::findOrFail($id);

            // Check if invoice has payments
            $hasPayments = $invoice->payments()->exists();
            if ($hasPayments) {
                toastr()->closeButton()->error('Cannot delete invoice with existing payments. Please delete payments first.');
                return redirect()->back();
            }

            $invoice->delete();

            toastr()->closeButton()->success('Invoice deleted successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error deleting invoice: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // ================================
    // View Invoice Details
    // ================================
    public function view_invoice_details($id)
    {
        $invoice = Invoices::with([
            'tenant.user',
            'house',
            'payments' => function($query) {
                $query->latest();
            }
        ])->findOrFail($id);

        return view('admin.invoice.view_details', compact('invoice'));
    }

    // ================================
    // Mark Invoice as Paid (Quick Action)
    // ================================
    public function mark_invoice_paid($id)
    {
        try {
            $invoice = Invoices::findOrFail($id);

            if ($invoice->status === 'paid') {
                toastr()->closeButton()->info('Invoice is already marked as paid.');
                return redirect()->back();
            }

            $invoice->status = 'paid';
            $invoice->paid_amount = $invoice->amount;
            $invoice->save();

            toastr()->closeButton()->success('Invoice marked as paid successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // ================================
    // Get Tenant's House (AJAX)
    // ================================
    public function get_tenant_house($tenant_id)
    {
        try {
            $tenant = Tenant::with('house:id,title,location,price')->find($tenant_id);

            if (!$tenant || !$tenant->house) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant or house not found'
                ]);
            }

            return response()->json([
                'success' => true,
                'house' => $tenant->house
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ================================
    // Generate Invoice for Tenant (Bulk/Monthly)
    // ================================
    public function generate_monthly_invoices()
    {
        try {
            $tenants = Tenant::with(['user', 'house'])->get();
            $generatedCount = 0;

            foreach ($tenants as $tenant) {
                // Check if invoice already exists for current month
                $existingInvoice = Invoices::where('tenant_id', $tenant->id)
                    ->whereMonth('issued_date', now()->month)
                    ->whereYear('issued_date', now()->year)
                    ->first();

                if (!$existingInvoice) {
                    $invoice = new Invoices();
                    $invoice->reference = Invoices::generateReference('INV');
                    $invoice->tenant_id = $tenant->id;
                    $invoice->house_id = $tenant->house_id;
                    $invoice->amount = $tenant->rent + $tenant->utilities;
                    $invoice->currency = 'KES';
                    $invoice->description = 'Monthly Rent for ' . now()->format('F Y');
                    $invoice->issued_date = now();
                    $invoice->due_date = now()->addDays(7); // 7 days to pay
                    $invoice->status = 'unpaid';
                    $invoice->paid_amount = 0;
                    $invoice->save();

                    $generatedCount++;

                    // Send notification
                    if ($tenant->user && $tenant->user->email) {
                        Mail::raw(
                            "Hello {$tenant->user->name},\n\n" .
                            "Your monthly invoice has been generated.\n\n" .
                            "Invoice #: {$invoice->reference}\n" .
                            "Amount: KES {$invoice->amount}\n" .
                            "Due Date: {$invoice->due_date->format('d M Y')}\n\n" .
                            "Please log in to pay.",
                            function ($message) use ($tenant) {
                                $message->to($tenant->user->email)
                                    ->subject('Monthly Invoice - ' . now()->format('F Y'));
                            }
                        );
                    }
                }
            }

            toastr()->closeButton()->success("Generated {$generatedCount} invoices successfully.");
            return redirect()->back();

        } catch (\Exception $e) {
            toastr()->closeButton()->error('Error generating invoices: ' . $e->getMessage());
            return redirect()->back();
        }

    }



}
