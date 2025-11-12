<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\House;
use App\Models\Contract;
use App\Models\User;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Mail;
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

        // Get tenants for those houses - with user relationship
        $tenants = Tenant::with(['house', 'user'])
            ->whereIn('house_id', $houses->pluck('id'))
            ->get();

        // Get maintenance requests using landlord's user_id (matching admin/tenant structure)
        $requests = MaintenanceRequest::with(['tenant:id,name,email', 'landlord:id,name,email'])
            ->where('landlord_id', $user->id)
            ->latest()
            ->get();

        return view('home.landlord.dashboard', compact('landlord', 'houses', 'tenants', 'requests'));
    }

    public function viewMaintenanceRequests()
    {
        $user = Auth::user();

        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        // Get maintenance requests for this landlord
        $requests = MaintenanceRequest::with(['tenant:id,name,email', 'landlord:id,name,email'])
            ->where('landlord_id', $user->id)
            ->latest()
            ->paginate(15);

        // return view('home.landlord.maintenance_requests', compact('requests'));
    }

    public function updateMaintenanceStatus(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        try {
            $maintenance = MaintenanceRequest::where('landlord_id', $user->id)
                ->findOrFail($id);

            $maintenance->status = $validated['status'];
            $maintenance->save();

            return redirect()->back()->with('success', 'Maintenance request status updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function viewContracts()
    {
        $user = Auth::user();

        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        $contracts = \App\Models\Contract::where('landlord_id', $user->id)
            ->with(['tenant:id,name,email', 'house:id,title,location,price'])
            ->latest()
            ->paginate(10);

        return view('home.landlord.contracts', compact('contracts'));
    }

    public function viewContractDetails($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        $contract = Contract::where('landlord_id', $user->id)
            ->where('id', $id)
            ->with(['tenant:id,name,email,phone', 'house:id,title,location,price'])
            ->firstOrFail();

        return view('home.landlord.contract_details', compact('contract'));
    }

    public function viewTerminationContracts()
    {
        $user = Auth::user();

        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        $contracts = Contract::where('landlord_id', $user->id)
            ->whereIn('termination_status', ['pending', 'partial'])
            ->with(['tenant:id,name,email', 'house:id,title,location'])
            ->latest()
            ->paginate(10);

        return view('home.landlord.termination_contracts', compact('contracts'));
    }

    public function viewTerminationDetails($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        $contract = \App\Models\Contract::where('landlord_id', $user->id)
            ->where('id', $id)
            ->whereIn('termination_status', ['pending', 'partial', 'completed'])
            ->with(['tenant:id,name,email,phone', 'house:id,title,location,price'])
            ->firstOrFail();

        return view('home.landlord.termination_details', compact('contract'));
    }

    public function signTermination(Request $request, $id)
    {
        $validated = $request->validate([
            'signature' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            if ($user->usertype !== 'landlord') {
                abort(403, 'Unauthorized access.');
            }

            $contract = \App\Models\Contract::where('landlord_id', $user->id)
                ->where('id', $id)
                ->whereIn('termination_status', ['pending', 'partial'])
                ->firstOrFail();

            // Check if landlord already signed
            if ($contract->landlord_termination_signature) {
                return redirect()->back()->with('error', 'You have already signed the termination agreement.');
            }

            // Save termination signature
            $signatureData = $validated['signature'];
            $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
            $signatureData = str_replace(' ', '+', $signatureData);
            $signature = base64_decode($signatureData);

            $filename = 'termination_landlord_' . time() . '_' . uniqid() . '.png';
            $path = public_path('signatures');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            file_put_contents($path . '/' . $filename, $signature);

            // Update contract
            $contract->landlord_termination_signature = $filename;
            $contract->landlord_signed_termination_at = now();

            // Check if both parties signed
            if ($contract->tenant_termination_signature) {
                $contract->termination_status = 'completed';
                $contract->status = 'terminated';
                $contract->terminated_at = now();

                // Update house status to available
                $house = House::find($contract->house_id);
                if ($house) {
                    $house->status = 'available';
                    $house->save();
                }

                // Notify tenant
                $tenant = User::find($contract->tenant_id);
                if ($tenant && $tenant->email) {
                    Mail::raw(
                        "Hello {$tenant->name},\n\nThe contract termination for {$contract->house->title} has been completed.\n\nBoth parties have signed the termination agreement.",
                        function ($message) use ($tenant) {
                            $message->to($tenant->email)->subject('Contract Termination Completed');
                        }
                    );
                }

                $message = 'Termination agreement signed successfully! Contract is now terminated.';
            } else {
                $contract->termination_status = 'partial';
                $message = 'Your signature has been recorded. Waiting for tenant to sign.';
            }

            $contract->save();

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function requestTermination($id)
    {
        try {
            $user = Auth::user();

            if ($user->usertype !== 'landlord') {
                abort(403, 'Unauthorized access.');
            }

            $contract = Contract::where('landlord_id', $user->id)
                ->where('id', $id)
                ->where('status', 'signed')
                ->whereNull('termination_status')
                ->firstOrFail();

            // Initiate termination
            $contract->termination_status = 'pending';
            $contract->termination_initiated_at = now();
            $contract->termination_initiated_by = 'landlord';
            $contract->save();

            // Notify tenant
            $tenant = User::find($contract->tenant_id);
            if ($tenant && $tenant->email) {
                Mail::raw(
                    "Hello {$tenant->name},\n\nLandlord {$user->name} has requested termination for your contract at {$contract->house->title}.\n\nPlease log in to review and sign the termination agreement.",
                    function ($message) use ($tenant) {
                        $message->to($tenant->email)->subject('Contract Termination Request');
                    }
                );
            }

            return redirect()->back()->with('success', 'Termination request sent successfully. Waiting for tenant approval.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function downloadContract($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        $contract = Contract::where('landlord_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $file_path = public_path('contracts/' . $contract->contract_pdf);

        if (!file_exists($file_path)) {
            return redirect()->back()->with('error', 'Contract file not found.');
        }

        return response()->download($file_path);
    }

    public function createContract($tenant_id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'landlord') {
            abort(403, 'Unauthorized access.');
        }

        // Get landlord record
        $landlord = Landlord::where('user_id', $user->id)->firstOrFail();

        // Get tenant details
        $tenant = Tenant::with(['user', 'house'])
            ->where('landlord_id', $landlord->id)
            ->where('user_id', $tenant_id)
            ->firstOrFail();

        // Check if contract already exists
        $existingContract = Contract::where('tenant_id', $tenant_id)
            ->where('landlord_id', $user->id)
            ->where('house_id', $tenant->house_id)
            ->whereIn('status', ['pending', 'signed'])
            ->first();

        if ($existingContract) {
            return redirect()->route('landlord.dashboard')
                ->with('error', 'A contract already exists for this tenant.');
        }

        return view('home.landlord.create_contract', compact('tenant', 'landlord'));
    }

    public function storeContract(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'house_id' => 'required|exists:houses,id',
            'contract_pdf' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        try {
            $user = Auth::user();

            if ($user->usertype !== 'landlord') {
                abort(403, 'Unauthorized access.');
            }

            // Verify tenant belongs to this landlord
            $landlord = Landlord::where('user_id', $user->id)->firstOrFail();
            $tenant = Tenant::where('user_id', $validated['tenant_id'])
                ->where('landlord_id', $landlord->id)
                ->firstOrFail();

            // Check for existing contract
            $existingContract = Contract::where('tenant_id', $validated['tenant_id'])
                ->where('house_id', $validated['house_id'])
                ->whereIn('status', ['pending', 'signed'])
                ->first();

            if ($existingContract) {
                return redirect()->back()
                    ->with('error', 'A contract already exists for this tenant and house.')
                    ->withInput();
            }

            $contract = new Contract();
            $contract->landlord_id = $user->id;
            $contract->tenant_id = $validated['tenant_id'];
            $contract->house_id = $validated['house_id'];
            $contract->status = 'pending';

            // Handle PDF upload
            if ($request->hasFile('contract_pdf')) {
                $file = $request->file('contract_pdf');
                $filename = 'contract_' . time() . '_' . uniqid() . '.pdf';
                $path = public_path('contracts');

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                $file->move($path, $filename);
                $contract->contract_pdf = $filename;
            }

            $contract->save();

            // Send email notification to tenant
            $tenantUser = User::find($validated['tenant_id']);
            $house = House::find($validated['house_id']);

            if ($tenantUser && $tenantUser->email) {
                $contractUrl = url('/tenant/contracts');
                Mail::raw(
                    "Hello {$tenantUser->name},\n\n" .
                    "A new rental contract has been created for you by {$user->name}.\n\n" .
                    "Property: {$house->title}\n" .
                    "Location: {$house->location}\n\n" .
                    "Please log in to review and sign the contract: {$contractUrl}\n\n" .
                    "Best regards,\nProperty Management System",
                    function ($message) use ($tenantUser) {
                        $message->to($tenantUser->email)
                            ->subject('New Rental Contract - Action Required');
                    }
                );
            }

            return redirect()->route('landlord.contracts')
                ->with('success', 'Contract created successfully and notification sent to tenant.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating contract: ' . $e->getMessage())
                ->withInput();
        }
    }
}
