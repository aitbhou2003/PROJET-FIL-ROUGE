<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Medicament;
use App\Models\MovementStock;
use App\Models\Stock;
use App\Models\StockVente;
use App\Models\Vente;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $medicaments = Medicament::with(['categorie', 'stocks' => function ($q) {
            $q->where('is_actif', true)
                ->where('date_expiration', '>', now())
                ->where('quantite', '>', 0)
                ->orderBy('date_expiration', 'asc');
        }]);

        if ($request->filled('search')) {
            $medicaments->where('nom', 'like', '%' . $request->search . '%')
                ->orWhere('code_barre', 'like', '%' . $request->search . '%')
                ->orWhere('fabricant', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('categorie')) {
            $medicaments->where('categorie', $request->categorie);
        }
        $medicaments->whereHas('stocks', function ($q) {
            $q->where('quantite', '>', 0)
                ->where('date_expiration', '>', now())
                ->where('is_actif', true);
        });
        $medicaments = $medicaments->orderBy('nom', 'asc')
            ->paginate(12);



        $categories = Categorie::all();
        $panier = session()->get('panier', []);
        $totalPanier = collect($panier)->sum('total_ligne');
        $nbItemsPanier = count($panier);

        return view('ventes.index', compact(
            'medicaments',
            'categories',
            'panier',
            'totalPanier',
            'nbItemsPanier'
        ));
    }


    public function ajouterAuPanier(Request $request)
    {
        $validated = $request->validate([
            'medicament_id' => ['required', 'exists:medicaments,id'],
            'quantite'      => ['required', 'integer', 'min:1'],
        ]);

        $medicament = Medicament::with(['stocks' => function ($q) {
            $q->where('is_actif', true)
                ->where('quantite', '>', 0)
                ->orderBy('date_expiration', 'asc');
        }])->find($validated['medicament_id']);

        $stockTotal = $medicament->stocks->sum('quantite');
        if ($stockTotal < $validated['quantite']) {
            return redirect()->back()
                ->with('error', "Stock insuffisant (disponible : " . $stockTotal . ")");
        }

        $cheminOrdonnance = null;
        if ($medicament->ordonnance_requise) {
            $request->validate([
                'ordonnance' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            ]);
            $cheminOrdonnance = $request->file('ordonnance')->store('ordonnances', 'private');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        $medicaments = Medicament::with(['stock' => function ($query) {
            $query->where('quantite', '>', 0)
                ->where('date_expiration', '>', now())
                ->orderBy('date_expiration');
        }])->whereHas('stocks', function ($query) {
            $query->where('quantite', '>', 0);
        })->get();;

        return view('ventes.create', compact('medicaments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

    }

    /**
     * Display the specified resource.
     */
    public function show(Vente $vente)
    {
        //

        if (Auth::user()->isEmploye() && $vente->user_id !== Auth::id()) {
            abort(403);
        }

        $vente->load('stockVentes.stock.medicament', 'user');
        return view('ventes.show', compact('vente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vente $vente)
    {
        //
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            //code...
            foreach ($vente->stockVentes as $SV) {
                $stock = $SV->stock;
                $quantiteAvant = $stock->quantite;
                $stock->increment('quantite', $SV->quantite);
                MovementStock::created([
                    'stock_id' => $stock->id,
                    'user_id' => Auth::id(),
                    'type' => 'sortie',
                    'quantite' => $SV->quantite,
                    'quantite_avant' => $quantiteAvant,
                    'quantite_apres' => $quantiteAvant + $SV->quantite,
                    'motif' => 'Annulation  Vente #' . $vente->id,
                ]);

                $vente->stockVentes()->delete();
                $vente->delete();

                DB::commit();

                return redirect()->route('ventes.index')
                    ->with('success', 'Vente annulée et stock restauré.');
            }
        } catch (\Throwable $e) {
            //throw $th;
            DB::rollback();
            return back()->with('error', "Erreur dans l'annulation.");
        }
    }
}
