<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Stock;
use App\Models\User;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    //

    public function index()
    {
        $totalMedicaments = Medicament::count();
        $totalStocks = Stock::where('is_actif', true)->sum('quantite');
        $totalVentes = Vente::where('statut', 'terminee')->count();
        $totalEmployes = User::where('role_id', 2)->where('is_actif', true)->count();

        $ventesDuJour = Vente::whereDate('created_at', today())
            ->where('statut', 'terminee')
            ->sum('total_ttc');

        $stockFaible = Stock::with('medicament')
            ->whereColumn('quantite', '<=', 'seuil_minimum')
            ->where('is_actif', true)
            ->limit(5)
            ->get();

        $peremptionProche = Stock::with('medicament')
            ->where('date_expiration', '<=', now()->addDays(30))
            ->where('quantite', '>', 0)
            ->where('is_actif', true)
            ->limit(5)
            ->get();

        $topMedicaments = DB::table('stock_ventes')
            ->join('medicaments', 'stock_ventes.medicament_id', '=', 'medicaments.id')
            ->join('ventes', 'stock_ventes.vente_id', '=', 'ventes.id')
            ->whereMonth('ventes.created_at', now()->month)
            ->select('medicaments.nom', DB::raw('SUM(stock_ventes.quantite) as total_vendu'))
            ->groupBy('medicaments.id', 'medicaments.nom')
            ->orderByDesc('total_vendu')
            ->limit(5)
            ->get();

        $dernieresVentes = Vente::with(['user', 'stockVentes.medicament'])
            ->where('statut', 'terminee')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMedicaments',
            'totalStocks',
            'totalVentes',
            'totalEmployes',
            'ventesDuJour',
            'stockFaible',
            'peremptionProche',
            'topMedicaments',
            'dernieresVentes'
        ));
    }
}
