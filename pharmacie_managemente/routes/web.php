<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\VenteController;
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
    // stock
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::post('/stocks/{stock}/update', [StockController::class, 'update'])->name('stocks.update');
});


Route::middleware(['auth'])->group(function () {
    // vente 
    Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');
    Route::get('/ventes/nouvelle', [VenteController::class, 'create'])->name('ventes.create');
    Route::post('/ventes', [VenteController::class, 'store'])->name('ventes.store');
    Route::get('/ventes/{vente}', [VenteController::class, 'show'])->name('ventes.show');
    // stock
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
});

Route::post('/panier/ajouter', [VenteController::class, 'ajouterAuPanier'])
    ->name('panier.ajouter');
Route::put('/panier/{index}', [VenteController::class, 'modifierQuantitePanier'])
    ->name('panier.modifier');
Route::delete('/panier/{index}', [VenteController::class, 'retirerDuPanier'])
    ->name('panier.retirer');

