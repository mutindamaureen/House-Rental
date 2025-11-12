<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\House;
use App\Models\User;
use App\Models\MaintenanceRequest;
use App\Models\Invoices;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

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
            $daysRemaining = (int) Carbon::now()->diffInDays(
                Carbon::parse($tenant->lease_end_date),
                false
            );
        }

        // Fetch maintenance requests
        $requests = MaintenanceRequest::where('tenant_id', $user->id)
            ->with(['tenant:id,name,email', 'landlord:id,name,email'])
            ->latest()
            ->get();

        // Get payment statistics
        $totalPaid = Payments::where('tenant_id', $tenant->id)
            ->where('status', 'succeeded')
            ->sum('amount');

        $pendingInvoices = Invoices::where('tenant_id', $tenant->id)
            ->where('status', 'unpaid')
            ->count();

        $overdueInvoices = Invoices::where('tenant_id', $tenant->id)
            ->where('status', 'overdue')
            ->count();

        return view('home.tenants.dashboard', compact(
            'tenant',
            'daysRemaining',
            'requests',
            'totalPaid',
            'pendingInvoices',
            'overdueInvoices'
        ));
    }

    public function requestMaintenance(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        try {
            $user = Auth::user();
            $tenant = Tenant::where('user_id', $user->id)->firstOrFail();

            // Create maintenance request
            $maintenance = new MaintenanceRequest();
            $maintenance->tenant_id = $user->id;
            $maintenance->landlord_id = $tenant->landlord_id ?? null;
            $maintenance->house_name = $tenant->house->title ?? 'N/A';
            $maintenance->subject = $validated['subject'];
            $maintenance->description = $validated['description'];
            $maintenance->status = 'pending';
            $maintenance->save();

            // Email notification to landlord
            if ($maintenance->landlord_id) {
                $landlord = User::find($maintenance->landlord_id);

                if ($landlord && $landlord->email) {
                    Mail::raw(
                        "Hello {$landlord->name},\n\nA new maintenance request has been submitted.\n\nSubject: {$maintenance->subject}\nHouse: {$maintenance->house_name}\nTenant: {$user->name}\n\nPlease log in to view details.",
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

                    $message = urlencode(
                        "🔧 *New Maintenance Request*\n\n" .
                        "Tenant: " . $user->name . "\n" .
                        "House: " . $maintenance->house_name . "\n" .
                        "Location: " . ($tenant->house->location ?? 'N/A') . "\n\n" .
                        "Issue: " . $maintenance->subject . "\n" .
                        "Description: " . $maintenance->description . "\n\n" .
                        "Status: " . ucfirst($maintenance->status)
                    );

                    $whatsappUrl = "https://wa.me/{$landlordPhone}?text={$message}";

                    return redirect()->back()
                        ->with('success', 'Maintenance request submitted successfully!')
                        ->with('whatsapp_url', $whatsappUrl);
                }
            }

            return redirect()->back()->with('success', 'Maintenance request submitted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ================================
    // INVOICES SECTION
    // ================================

    public function viewInvoices()
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $tenant = Tenant::where('user_id', $user->id)->firstOrFail();

        $invoices = Invoices::where('tenant_id', $tenant->id)
            ->with(['house:id,title,location', 'payments'])
            ->latest()
            ->paginate(15);

        // Statistics
        $totalDue = Invoices::where('tenant_id', $tenant->id)
            ->where('status', 'unpaid')
            ->sum('amount');

        $totalPaid = Invoices::where('tenant_id', $tenant->id)
            ->where('status', 'paid')
            ->sum('amount');

        $overdueCount = Invoices::where('tenant_id', $tenant->id)
            ->where('status', 'overdue')
            ->count();

        return view('home.tenants.invoices', compact(
            'invoices',
            'totalDue',
            'totalPaid',
            'overdueCount'
        ));
    }

    public function viewInvoiceDetails($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $tenant = Tenant::where('user_id', $user->id)->firstOrFail();

        $invoice = Invoices::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with(['house:id,title,location', 'payments' => function($query) {
                $query->latest();
            }])
            ->firstOrFail();

        return view('home.tenants.invoice_details', compact('invoice'));
    }

    // ================================
    // PAYMENTS SECTION
    // ================================

    public function viewPayments()
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $tenant = Tenant::where('user_id', $user->id)->firstOrFail();

        $payments = Payments::where('tenant_id', $tenant->id)
            ->with(['invoice'])
            ->latest()
            ->paginate(15);

        // Statistics
        $totalPaid = Payments::where('tenant_id', $tenant->id)
            ->where('status', 'succeeded')
            ->sum('amount');

        $pendingPayments = Payments::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->sum('amount');

        $lastPayment = Payments::where('tenant_id', $tenant->id)
            ->where('status', 'succeeded')
            ->latest('paid_at')
            ->first();

        return view('home.tenants.payments', compact(
            'payments',
            'totalPaid',
            'pendingPayments',
            'lastPayment'
        ));
    }

    public function viewPaymentDetails($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $tenant = Tenant::where('user_id', $user->id)->firstOrFail();

        $payment = Payments::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with(['invoice'])
            ->firstOrFail();

        return view('home.tenants.payment_details', compact('payment'));
    }

    public function initiatePayment(Request $request, $invoice_id)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:card,mpesa,paybill,till,bank_transfer',
            'phone_number' => 'required_if:payment_method,mpesa|nullable|string|max:20',
        ]);

        try {
            $user = Auth::user();
            $tenant = Tenant::where('user_id', $user->id)->firstOrFail();

            $invoice = Invoices::where('tenant_id', $tenant->id)
                ->where('id', $invoice_id)
                ->where('status', 'unpaid')
                ->firstOrFail();

            // Create payment record
            $payment = new Payments();
            $payment->invoice_id = $invoice->id;
            $payment->tenant_id = $tenant->id;
            $payment->amount = $invoice->amount - $invoice->paid_amount;
            $payment->currency = $invoice->currency;
            $payment->payment_method = $validated['payment_method'];
            $payment->status = 'pending';
            $payment->merchant_reference = 'inv-' . $invoice->id . '-' . time();
            $payment->idempotency_key = \Illuminate\Support\Str::uuid();
            $payment->save();

            // Here you would integrate with actual payment gateway
            // For M-Pesa: trigger STK push
            // For Card: redirect to payment page
            // etc.

            return redirect()->back()->with('success', 'Payment initiated successfully. Please complete the payment.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error initiating payment: ' . $e->getMessage());
        }
    }

    public function downloadInvoice($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $tenant = Tenant::where('user_id', $user->id)->firstOrFail();

        $invoice = Invoices::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with(['house', 'tenant.user'])
            ->firstOrFail();

        // Generate PDF or return view for printing
        return view('home.tenants.invoice_pdf', compact('invoice'));
    }

    public function downloadPaymentReceipt($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $tenant = Tenant::where('user_id', $user->id)->firstOrFail();

        $payment = Payments::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->where('status', 'succeeded')
            ->with(['invoice', 'tenant.user'])
            ->firstOrFail();

        // Generate PDF or return view for printing
        return view('home.tenants.payment_receipt', compact('payment'));
    }

    // ================================
    // CONTRACTS SECTION (Existing)
    // ================================

    public function viewContracts()
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $contracts = \App\Models\Contract::where('tenant_id', $user->id)
            ->with(['landlord:id,name,email', 'house:id,title,location,price'])
            ->latest()
            ->paginate(10);

        return view('home.tenants.contracts', compact('contracts'));
    }

    public function viewContractDetails($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $contract = \App\Models\Contract::where('tenant_id', $user->id)
            ->where('id', $id)
            ->with(['landlord:id,name,email,phone', 'house:id,title,location,price'])
            ->firstOrFail();

        return view('home.tenants.contract_details', compact('contract'));
    }

    public function downloadContract($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $contract = \App\Models\Contract::where('tenant_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $file_path = public_path('contracts/' . $contract->contract_pdf);

        if (!file_exists($file_path)) {
            return redirect()->back()->with('error', 'Contract file not found.');
        }

        return response()->download($file_path);
    }

    public function signContract(Request $request, $id)
    {
        $validated = $request->validate([
            'signature' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            if ($user->usertype !== 'tenant') {
                abort(403, 'Unauthorized access.');
            }

            $contract = \App\Models\Contract::where('tenant_id', $user->id)
                ->where('id', $id)
                ->where('status', 'pending')
                ->firstOrFail();

            // Save signature image
            $signatureData = $validated['signature'];
            $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
            $signatureData = str_replace(' ', '+', $signatureData);
            $signature = base64_decode($signatureData);

            $filename = 'signature_' . time() . '_' . uniqid() . '.png';
            $path = public_path('signatures');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            file_put_contents($path . '/' . $filename, $signature);

            // Update contract
            $contract->tenant_signature = $filename;
            $contract->status = 'signed';
            $contract->signed_at = now();
            $contract->save();

            // Email notification to landlord
            $landlord = User::find($contract->landlord_id);
            if ($landlord && $landlord->email) {
                Mail::raw(
                    "Hello {$landlord->name},\n\nTenant {$user->name} has signed the contract for {$contract->house->title}.\n\nYou can view the signed contract in your dashboard.",
                    function ($message) use ($landlord) {
                        $message->to($landlord->email)
                            ->subject('Contract Signed - ' . date('Y-m-d'));
                    }
                );
            }

            return redirect()->back()->with('success', 'Contract signed successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error signing contract: ' . $e->getMessage());
        }
    }

    public function viewTerminationContracts()
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $contracts = \App\Models\Contract::where('tenant_id', $user->id)
            ->whereIn('termination_status', ['pending', 'partial'])
            ->with(['landlord:id,name,email', 'house:id,title,location'])
            ->latest()
            ->paginate(10);

        return view('home.tenants.termination_contracts', compact('contracts'));
    }

    public function viewTerminationDetails($id)
    {
        $user = Auth::user();

        if ($user->usertype !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        $contract = \App\Models\Contract::where('tenant_id', $user->id)
            ->where('id', $id)
            ->whereIn('termination_status', ['pending', 'partial', 'completed'])
            ->with(['landlord:id,name,email,phone', 'house:id,title,location,price'])
            ->firstOrFail();

        return view('home.tenants.termination_details', compact('contract'));
    }

    public function signTermination(Request $request, $id)
    {
        $validated = $request->validate([
            'signature' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            if ($user->usertype !== 'tenant') {
                abort(403, 'Unauthorized access.');
            }

            $contract = \App\Models\Contract::where('tenant_id', $user->id)
                ->where('id', $id)
                ->whereIn('termination_status', ['pending', 'partial'])
                ->firstOrFail();

            if ($contract->tenant_termination_signature) {
                return redirect()->back()->with('error', 'You have already signed the termination agreement.');
            }

            // Save termination signature
            $signatureData = $validated['signature'];
            $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
            $signatureData = str_replace(' ', '+', $signatureData);
            $signature = base64_decode($signatureData);

            $filename = 'termination_tenant_' . time() . '_' . uniqid() . '.png';
            $path = public_path('signatures');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            file_put_contents($path . '/' . $filename, $signature);

            // Update contract
            $contract->tenant_termination_signature = $filename;
            $contract->tenant_signed_termination_at = now();

            if ($contract->landlord_termination_signature) {
                $contract->termination_status = 'completed';
                $contract->status = 'terminated';
                $contract->terminated_at = now();

                $house = House::find($contract->house_id);
                if ($house) {
                    $house->status = 'available';
                    $house->save();
                }

                $landlord = User::find($contract->landlord_id);
                if ($landlord && $landlord->email) {
                    Mail::raw(
                        "Hello {$landlord->name},\n\nThe contract termination for {$contract->house->title} has been completed.\n\nBoth parties have signed the termination agreement.",
                        function ($message) use ($landlord) {
                            $message->to($landlord->email)->subject('Contract Termination Completed');
                        }
                    );
                }

                $message = 'Termination agreement signed successfully! Contract is now terminated.';
            } else {
                $contract->termination_status = 'partial';
                $message = 'Your signature has been recorded. Waiting for landlord to sign.';
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

            if ($user->usertype !== 'tenant') {
                abort(403, 'Unauthorized access.');
            }

            $contract = \App\Models\Contract::where('tenant_id', $user->id)
                ->where('id', $id)
                ->where('status', 'signed')
                ->whereNull('termination_status')
                ->firstOrFail();

            $contract->termination_status = 'pending';
            $contract->termination_initiated_at = now();
            $contract->termination_initiated_by = 'tenant';
            $contract->save();

            $landlord = User::find($contract->landlord_id);
            if ($landlord && $landlord->email) {
                Mail::raw(
                    "Hello {$landlord->name},\n\nTenant {$user->name} has requested termination for the contract at {$contract->house->title}.\n\nPlease log in to review and sign the termination agreement.",
                    function ($message) use ($landlord) {
                        $message->to($landlord->email)->subject('Contract Termination Request');
                    }
                );
            }

            return redirect()->back()->with('success', 'Termination request sent successfully. Waiting for landlord approval.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
