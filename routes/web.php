<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\ContractController;

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
