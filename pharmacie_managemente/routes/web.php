<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MedicamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// login page
Route::get('/login', [LoginController::class, 'index'])
    ->name('login');

// login store
Route::post('/login', [LoginController::class, 'store'])
    ->name('login.store');



// logout
Route::post('/logout', [LogoutController::class, 'logout'])
    ->name('logout');

// admin dashboared
Route::get('/admin/dashboard', function () {
    return view('dashboard.admin');
})->name('dashboard.admin')
    ->middleware('role:admin');

// empluye dashboared  
Route::get('/employe/dashboard', function () {
    return view('dashboard.employe');
})->name('dashboard.employe');

Route::middleware(['role:admin'])->group(function () {
    Route::get('/register', [RegisterController::class, 'index'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register.store');

    // medicaenmnts
    Route::resource('medicaments', MedicamentController::class);
});
