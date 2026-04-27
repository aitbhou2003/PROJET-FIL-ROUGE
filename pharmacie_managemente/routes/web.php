<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\RecuController;
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
    ->middleware('checkRole:admin');

// empluye dashboared  
Route::get('/employe/dashboard', function () {
    return view('dashboard.employe');
})->name('dashboard.employe');

Route::middleware(['checkRole:admin'])->group(function () {
    Route::get('/register', [RegisterController::class, 'index'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register.store');

    // medicaenmnts
    Route::resource('medicaments', MedicamentController::class);
    // stock
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::post('/stocks/{stock}/update', [StockController::class, 'update'])->name('stocks.update');
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
});


// Route::middleware(['auth'])->group(function () {
//     // vente 
//     Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');
//     Route::post('/ventes', [VenteController::class, 'store'])->name('ventes.store');
//     Route::get('/ventes/{vente}', [VenteController::class, 'show'])->name('ventes.show');
//     // s tock
Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
// });

Route::middleware(['auth', 'checkRole:employe'])->group(function () {
    // === VENTE (Page POS principale) ===
    Route::get('/ventes/nouvelle', [VenteController::class, 'create'])->name('ventes.create');
    Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');

    // === PANIER ===
    Route::post('/panier/ajouter', [PanierController::class, 'ajouterAuPanier'])->name('panier.ajouter');
    Route::put('/panier/{index}', [PanierController::class, 'modifierQuantitePanier'])->name('panier.modifier');
    Route::delete('/panier/{index}', [PanierController::class, 'retirerDuPanier'])->name('panier.retirer');
    Route::post('/panier/remise', [PanierController::class, 'appliquerRemise'])->name('panier.remise');

    // === CHECKOUT ===
    Route::get('/ventes/checkout', [CheckoutController::class, 'checkout'])->name('ventes.checkout');
    Route::post('/ventes/finaliser', [CheckoutController::class, 'finaliserVente'])->name('ventes.finaliser');
    Route::delete('/ventes/checkout/remise', [PanierController::class, 'supprimerRemise'])->name('ventes.checkout.remise.supprimer');

    // === REÇU ===
    Route::get('/ventes/consulter/{vente}', [RecuController::class, 'consulterRecu'])->name('ventes.consulterRecu');
    Route::get('/ventes/recu/{vente}', [RecuController::class, 'recu'])->name('ventes.recu');
});
