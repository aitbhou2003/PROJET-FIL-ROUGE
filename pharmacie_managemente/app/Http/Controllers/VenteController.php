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
        $validated = $request->validate([
            'nom_client' => ['nullable', 'string', 'max:255'],
            'mode_paiement' => ['required', 'in:espece,carte,mobile'],
            'remise' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.stock_id' => ['required', 'exists:stocks,id'],
            'items.*.quantite' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $vente = Vente::create([
                'user_id' => Auth::id(),
                'nom_client' => $validated['nom_client'] ?? 'Client',
                'mode_paiement' => $validated['mode_paiement'],
                'remise' => $validated['remise'] ?? 0,
                'total' => 0,

            ]);
            $totale = 0;
            foreach ($validated['items'] as $item) {
                $stock = Stock::find($item['stock_id']);
                if ($stock->quantite < $item['quantite']) {
                    throw new Exception("Stock insuffisant pour " . $stock->medicament->nom);
                }

                $sousTotale = $item['quantite'] * $stock->prix_vente;
                $totale += $sousTotale;

                StockVente::create([
                    'vente_id' => $vente->id,
                    'stock_id' => $stock->id,
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $stock->prix_vente,
                    'total' => $sousTotale,
                ]);

                $quantiteAvant = $stock->quantite;
                $stock->decrement('quantite', $item['quantite']);

                MovementStock::create([
                    'stock_id' => $stock->id,
                    'user_id' => Auth::id(),
                    'type' => 'sortie',
                    'quantite' => $item['quantite'],
                    'quantite_avant' => $quantiteAvant,
                    'quantite_apres' => $quantiteAvant - $item['quantite'],
                    'motif' => 'Vente #' . $vente->id . ' - ' . $vente->nom_client,
                ]);

                $remise = $validated['remise'] ?? 0;
                $totalFinal = $totale - $remise;
                $vente->update(['total' => $totalFinal]);

                DB::commit();
                return redirect()->route('ventes.show', $vente)
                    ->with('success', 'Vente enregistrée ,Total: ' . number_format($totalFinal, 2) . 'Dh');
            }
        } catch (\Throwable $e) {
            //throw $th;
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
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
