<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\House;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Ensure only tenants can access
        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        // Fetch tenant record
        $tenant = Tenant::with('house')->where('user_id', $user->id)->first();

        if (!$tenant) {
            return view('home.tenants.no_house');
        }

        // Calculate days remaining till lease ends
        $daysRemaining = null;
        if ($tenant->lease_end_date) {
            $daysRemaining = Carbon::now()->diffInDays(Carbon::parse($tenant->lease_end_date), false);
        }

        // Maintenance requests (optional model)
        $requests = MaintenanceRequest::where('tenant_id', $tenant->id)->latest()->get();

        return view('home.tenants.dashboard', compact('tenant', 'daysRemaining', 'requests'));
    }

    public function requestMaintenance(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();

        $maintenance = new MaintenanceRequest();
        $maintenance->tenant_id = $tenant->id;
        $maintenance->house_id = $tenant->house_id;
        $maintenance->subject = $request->subject;
        $maintenance->description = $request->description;
        $maintenance->status = 'Pending';
        // $maintenance->status = $request->status;

        $maintenance->save();

        return redirect()->back()->with('success', 'Maintenance request submitted successfully!');
    }
}
