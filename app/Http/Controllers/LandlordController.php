<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\House;
use App\Models\MaintenanceRequest;

class LandlordController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Only landlords allowed
        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        // Get landlord record
        $landlord = Landlord::where('user_id', $user->id)->firstOrFail();

        // Get all houses owned by landlord
        $houses = House::where('landlord_id', $landlord->id)->get();

        // Get tenants for those houses
        $tenants = Tenant::with('house')->whereIn('house_id', $houses->pluck('id'))->get();

        // Get maintenance requests for landlord’s houses
        $requests = MaintenanceRequest::with(['tenant', 'house'])
            ->whereIn('house_id', $houses->pluck('id'))
            ->latest()
            ->get();

        return view('home.landlord.dashboard', compact('landlord', 'houses', 'tenants', 'requests'));
    }
}
