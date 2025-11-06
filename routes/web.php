<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/',[HomeController::class, 'home']);

Route::get('/dashboard', function () {
    return view('home.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



// Category routes

// Route::get('admin/dashboard', [HomeController::class, 'index'])->
//     middleware(['auth', 'admin']);

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
