<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\ChatController;

Route::middleware(['auth'])->group(function () {
    Route::get('/landlord/dashboard', [LandlordController::class, 'dashboard'])->name('landlord.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::middleware('tenant')->group(function () {
        Route::get('/tenant/dashboard', [TenantController::class, 'dashboard'])->name('tenant.dashboard');
        Route::post('/tenant/request-maintenance', [TenantController::class, 'requestMaintenance'])->name('tenant.requestMaintenance');
    });
});

Route::get('/',[HomeController::class, 'home']);
Route::get('/dashboard',[HomeController::class, 'login_home'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/houses', [HomeController::class, 'houses'])->name('houses');
Route::get('/see_house', [HomeController::class, 'see_house'])->name('see_house');
Route::get('/house_details/{id}', [HomeController::class, 'house_details'])->name('house.details');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



// Category routes


Route::get('admin/dashboard', [AdminController::class, 'index'])->
    middleware(['auth', 'admin']);

Route::get('view_category', [AdminController::class, 'view_category'])->
    middleware(['auth', 'admin']);

Route::post('add_category', [AdminController::class, 'add_category'])->
    middleware(['auth', 'admin']);

Route::get('edit_category/{id}', [AdminController::class, 'edit_category'])->
    middleware(['auth', 'admin']);

Route::post('update_category/{id}', [AdminController::class, 'update_category'])->
    middleware(['auth', 'admin']);


Route::get('delete_category/{id}', [AdminController::class, 'delete_category'])->
    middleware(['auth', 'admin']);




// House routes

Route::get('add_house', [AdminController::class, 'add_house'])->
    middleware(['auth', 'admin']);

Route::post('upload_house', [AdminController::class, 'upload_house'])->
    middleware(['auth', 'admin']);

Route::get('view_house', [AdminController::class, 'view_house'])->
    middleware(['auth', 'admin']);

Route::get('delete_house/{id}', [AdminController::class, 'delete_house'])->
    middleware(['auth', 'admin']);

Route::get('edit_house/{id}', [AdminController::class, 'edit_house'])->
    middleware(['auth', 'admin']);

Route::post('update_house/{id}', [AdminController::class, 'update_house'])->
    middleware(['auth', 'admin']);

Route::get('search_house', [AdminController::class, 'search_house'])->
    middleware(['auth', 'admin']);



// User routes

Route::get('add_user', [AdminController::class, 'add_user'])->
    middleware(['auth', 'admin']);

Route::post('upload_user', [AdminController::class, 'upload_user'])->
    middleware(['auth', 'admin']);

Route::get('view_user', [AdminController::class, 'view_user'])->
    middleware(['auth', 'admin']);

Route::get('delete_user/{id}', [AdminController::class, 'delete_user'])->
    middleware(['auth', 'admin']);

Route::get('edit_user/{id}', [AdminController::class, 'edit_user'])->
    middleware(['auth', 'admin']);

Route::post('update_user/{id}', [AdminController::class, 'update_user'])->
    middleware(['auth', 'admin']);




// Tenants routes

Route::get('add_tenant', [AdminController::class, 'add_tenant'])->
    middleware(['auth', 'admin']);

Route::post('upload_tenant', [AdminController::class, 'upload_tenant'])->
    middleware(['auth', 'admin']);

Route::get('view_tenant', [AdminController::class, 'view_tenant'])->
    middleware(['auth', 'admin']);

Route::get('delete_tenant/{id}', [AdminController::class, 'delete_tenant'])->
    middleware(['auth', 'admin']);

Route::get('edit_tenant/{id}', [AdminController::class, 'edit_tenant'])->
    middleware(['auth', 'admin']);

Route::post('update_tenant/{id}', [AdminController::class, 'update_tenant'])->
    middleware(['auth', 'admin']);




// Landlords routes

Route::get('add_landlord', [AdminController::class, 'add_landlord'])->
    middleware(['auth', 'admin']);

Route::post('upload_landlord', [AdminController::class, 'upload_landlord'])->
    middleware(['auth', 'admin']);

Route::get('view_landlord', [AdminController::class, 'view_landlord'])->
    middleware(['auth', 'admin']);

Route::get('delete_landlord/{id}', [AdminController::class, 'delete_landlord'])->
    middleware(['auth', 'admin']);

Route::get('edit_landlord/{id}', [AdminController::class, 'edit_landlord'])->
    middleware(['auth', 'admin']);

Route::post('update_landlord/{id}', [AdminController::class, 'update_landlord'])->
    middleware(['auth', 'admin']);

// Maintenance Request routes
// Add this route in your admin routes group
Route::get('/admin/tenant-details/{id}', [AdminController::class, 'get_tenant_details'])
    ->name('admin.tenant.details');

Route::get('add_maintenancerequest', [AdminController::class, 'add_maintenancerequest'])->
    middleware(['auth', 'admin']);

Route::post('upload_maintenancerequest', [AdminController::class, 'upload_maintenancerequest'])->
    middleware(['auth', 'admin']);

Route::get('view_maintenancerequest', [AdminController::class, 'view_maintenancerequest'])->
    middleware(['auth', 'admin']);

Route::get('delete_maintenancerequest/{id}', [AdminController::class, 'delete_maintenancerequest'])->
    middleware(['auth', 'admin']);

Route::get('edit_maintenancerequest/{id}', [AdminController::class, 'edit_maintenancerequest'])->
    middleware(['auth', 'admin']);

Route::post('update_maintenancerequest/{id}', [AdminController::class, 'update_maintenancerequest'])->
    middleware(['auth', 'admin']);
// routes/web.php


Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('invoices', [AdminController::class, 'view_invoice'])->name('invoices.index');

    Route::get('invoices/create', [AdminController::class, 'add_invoice'])->name('invoices.create');

    Route::post('invoices', [AdminController::class, 'upload_invoice'])->name('invoices.store');

    // View invoice details
    Route::get('invoices/{id}', [AdminController::class, 'view_invoice_details'])->name('invoices.show');

    // Show edit form
    Route::get('invoices/{id}/edit', [AdminController::class, 'edit_invoice'])->name('invoices.edit');

    // Update invoice
    Route::match(['put', 'patch'], 'invoices/{id}', [AdminController::class, 'update_invoice'])->name('invoices.update');

    // Delete invoice
    Route::delete('invoices/{id}', [AdminController::class, 'delete_invoice'])->name('invoices.destroy');

    // Quick action: mark invoice paid
    Route::post('invoices/{id}/mark-paid', [AdminController::class, 'mark_invoice_paid'])->name('invoices.markPaid');

    // AJAX: get tenant's house
    Route::get('tenants/{tenant_id}/house', [AdminController::class, 'get_tenant_house'])->name('tenants.house');

    // Generate monthly/bulk invoices (trigger manually or from scheduler via route)
    Route::post('invoices/generate-monthly', [AdminController::class, 'generate_monthly_invoices'])->name('invoices.generateMonthly');
});

Route::prefix('admin')->middleware(['auth','admin'])->group(function () {
    Route::get('payments', [AdminController::class, 'view_payments'])->name('admin.view_payments');
    Route::get('payments/create', [AdminController::class, 'add_payment'])->name('admin.add_payment');
    Route::post('payments', [AdminController::class, 'upload_payment'])->name('admin.upload_payment');
    Route::get('payments/{id}', [AdminController::class, 'view_payment'])->name('admin.view_payment');
    Route::delete('payments/{id}', [AdminController::class, 'delete_payment'])->name('admin.delete_payment');

});






Route::get('/view_contract', [AdminController::class, 'view_contract'])->
    middleware(['auth', 'admin']);
Route::get('/add_contract', [AdminController::class, 'add_contract'])->
    middleware(['auth', 'admin']);
Route::post('/upload_contract', [AdminController::class, 'upload_contract'])->
    middleware(['auth', 'admin']);
Route::get('/edit_contract/{id}', [AdminController::class, 'edit_contract'])->
    middleware(['auth', 'admin']);
Route::post('/update_contract/{id}', [AdminController::class, 'update_contract'])->
    middleware(['auth', 'admin']);
Route::get('/delete_contract/{id}', [AdminController::class, 'delete_contract'])->
    middleware(['auth', 'admin']);
Route::get('/download_contract/{id}', [AdminController::class, 'download_contract'])->
    middleware(['auth', 'admin']);
Route::get('/view_contract_details/{id}', [AdminController::class, 'view_contract_details'])->
    middleware(['auth', 'admin']);
Route::get('/get_tenants_by_landlord/{landlord_id}', [AdminController::class, 'get_tenants_by_landlord']);
Route::get('/get_house_by_tenant/{tenant_id}', [AdminController::class, 'get_house_by_tenant']);
Route::post('/contract/initiate-termination/{id}', [AdminController::class, 'initiateTermination']);
Route::get('/contract/termination/{id}', [AdminController::class, 'viewTerminationDetails']);
Route::post('/contract/cancel-termination/{id}', [AdminController::class, 'cancelTermination']);
Route::get('/terminated_contracts', [AdminController::class, 'terminated_contracts'])
    ->middleware(['auth', 'admin']);



// Tenant Contract Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/tenant/contracts', [TenantController::class, 'viewContracts'])->name('tenant.contracts');
    Route::get('/tenant/contract/{id}', [TenantController::class, 'viewContractDetails'])->name('tenant.contract.details');
    Route::get('/tenant/contract/download/{id}', [TenantController::class, 'downloadContract'])->name('tenant.contract.download');
    Route::post('/tenant/contract/sign/{id}', [TenantController::class, 'signContract'])->name('tenant.contract.sign');
    Route::get('/tenant/termination-contracts', [TenantController::class, 'viewTerminationContracts'])->name('tenant.termination.contracts');
    Route::get('/tenant/termination/{id}', [TenantController::class, 'viewTerminationDetails'])->name('tenant.termination.details');
    Route::post('/tenant/termination/sign/{id}', [TenantController::class, 'signTermination'])->name('tenant.termination.sign');
    Route::post('/tenant/contract/request-termination/{id}', [TenantController::class, 'requestTermination'])->name('tenant.contract.request-termination');
});

// Tenant Invoices & Payments Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/tenant/invoices', [TenantController::class, 'viewInvoices'])->name('tenant.invoices');
    Route::get('/tenant/invoices/{id}', [TenantController::class, 'viewInvoiceDetails'])->name('tenant.invoice.details');
    Route::get('/tenant/invoices/{id}/download', [TenantController::class, 'downloadInvoice'])->name('tenant.invoice.download');

    Route::get('/tenant/payments', [TenantController::class, 'viewPayments'])->name('tenant.payments');
    Route::get('/tenant/payments/{id}', [TenantController::class, 'viewPaymentDetails'])->name('tenant.payment.details');
    Route::post('/tenant/payments/initiate/{invoice_id}', [TenantController::class, 'initiatePayment'])->name('tenant.payment.initiate');
    Route::get('/tenant/payments/{id}/receipt', [TenantController::class, 'downloadPaymentReceipt'])->name('tenant.payment.receipt');
});

// Landlord Contract & Termination Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/landlord/contracts', [LandlordController::class, 'viewContracts'])->name('landlord.contracts');
    Route::get('/landlord/contract/{id}', [LandlordController::class, 'viewContractDetails'])->name('landlord.contract.details');
    Route::get('/landlord/contract/download/{id}', [LandlordController::class, 'downloadContract'])->name('landlord.contract.download');
    Route::get('/landlord/termination-contracts', [LandlordController::class, 'viewTerminationContracts'])->name('landlord.termination.contracts');
    Route::get('/landlord/termination/{id}', [LandlordController::class, 'viewTerminationDetails'])->name('landlord.termination.details');
    Route::post('/landlord/termination/sign/{id}', [LandlordController::class, 'signTermination'])->name('landlord.termination.sign');
    Route::post('/landlord/contract/request-termination/{id}', [LandlordController::class, 'requestTermination'])->name('landlord.contract.request-termination');
    Route::post('/landlord/maintenance/{id}/update', [LandlordController::class, 'updateMaintenanceStatus'])->name('landlord.maintenance.update');
    Route::get('/landlord/contracts/create/{tenant_id}', [LandlordController::class, 'createContract'])->name('landlord.contract.create');
    Route::post('/landlord/contracts/store', [LandlordController::class, 'storeContract'])->name('landlord.contract.store');
    // Route::post('/landlord/termination/{id}/request', [LandlordController::class, 'requestTermination'])->name('landlord.termination.request');


});


// ===================================
// USER CHAT ROUTES (Protected by auth)
// ===================================
Route::middleware(['auth'])->group(function () {
    // Chat list
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');

    // View specific conversation
    Route::get('/chat/{houseId}/{landlordId}', [ChatController::class, 'show'])->name('chat.show');

    // Send message
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Get messages (AJAX)
    Route::get('/chat/messages/{houseId}/{otherUserId}', [ChatController::class, 'getMessages'])->name('chat.messages');

    // Mark as read (AJAX)
    Route::post('/chat/mark-read/{houseId}/{otherUserId}', [ChatController::class, 'markAsRead'])->name('chat.mark-read');

    // Unread count (AJAX)
    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
});

// ===================================
// ADMIN CHAT ROUTES
// ===================================
Route::middleware(['auth', 'admin'])->group(function () {
    // View all conversations
    Route::get('/admin/chat', [ChatController::class, 'adminIndex'])->name('admin.chat.index');

    // View specific conversation
    Route::get('/admin/chat/{senderId}/{receiverId}/{houseId}', [ChatController::class, 'adminShow'])->name('admin.chat.show');

    // Delete message
    Route::delete('/admin/chat/delete/{id}', [ChatController::class, 'deleteMessage'])->name('admin.chat.delete');
});


// // Chat routes (requires authentication)
// Route::middleware(['auth'])->group(function () {
//     // Chat inbox
//     Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');

//     // View conversation with specific user
//     Route::get('/chat/{userId}', [ChatController::class, 'show'])->name('chat.show');

//     // Send message
//     Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.store');

//     // Get new messages (AJAX)
//     Route::get('/chat/{userId}/new-messages', [ChatController::class, 'getNewMessages']);

//     // Get unread count (AJAX)
//     Route::get('/chat/unread/count', [ChatController::class, 'getUnreadCount']);

//     // Start chat from house details
//     Route::post('/chat/start/{houseId}', [ChatController::class, 'startChat'])->name('chat.start');
//     Route::get('/chat/start/{houseId}', [ChatController::class, 'startChat'])->name('chat.start');

//     // Delete conversation
//     Route::delete('/chat/{userId}/delete', [ChatController::class, 'deleteConversation'])->name('chat.delete');
// });


// Route::middleware(['auth', 'admin'])->group(function () {

//     // Chat Management
//     Route::get('/admin/chats', [AdminController::class, 'view_chats'])->name('admin.chats');
//     Route::delete('/admin/chat/delete/{id}', [AdminController::class, 'delete_chat_message']);
//     Route::get('/admin/chat/conversation/{userId1}/{userId2}', [AdminController::class, 'view_conversation']);
//     Route::delete('/admin/chat/conversation/{userId1}/{userId2}', [AdminController::class, 'delete_conversation']);
// });
