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
            $cheminOrdonnance = $request->file('ordonnance')->store('ordonnances', 'public');
        }

        $lots = $this->calculerLots($medicament->stocks, $validated['quantite']);
        $prixUnitaire = $medicament->stocks->first()->prix_vente;
        $item = [
            'medicament_id'       => $medicament->id,
            'nom'                 => $medicament->nom,
            'quantite'            => $validated['quantite'],
            'prix_unitaire'       => $prixUnitaire,
            'total_ligne'         => $prixUnitaire * $validated['quantite'],
            'lots'                => $lots,
            'requiert_ordonnance' => $medicament->ordonnance_requise,
            'ordonnance'          => $cheminOrdonnance,      // null si pas d'ordonnance
        ];


        $panier = session()->get('panier', []);
        $existe = false;

        foreach ($panier as $key => $p) {
            if ($p['medicament_id'] == $medicament->id) {
                $nouvelleQt = $p['quantite'] + $validated['quantite'];
                if ($stockTotal < $nouvelleQt) {
                    return redirect()->back()
                        ->with('error', "Stock insuffisant (max : {$stockTotal})");
                }
                $lots = $this->calculerLots($medicament->stocks, $nouvelleQt);
                $panier[$key]['quantite']    = $nouvelleQt;
                $panier[$key]['total_ligne'] = $prixUnitaire * $nouvelleQt;
                $panier[$key]['lots']        = $lots;
                if ($medicament->ordonnance_requise && empty($panier[$key]['ordonnance'])) {
                    $panier[$key]['ordonnance'] = $cheminOrdonnance;
                }
                $existe = true;
                break;
            }
        }

        if (! $existe) {
            $panier[] = $item;
        }

        session()->put('panier', $panier);
        return redirect()->back()
            ->with('success', 'Produit ajouté au panier' . ($medicament->ordonnance_requise ? ' (ordonnance enregistrée)' : ''));
    }

    private function calculerLots($stocks, $qtDemandee)
    {
        $lots = [];
        $reste = $qtDemandee;

        foreach ($stocks as $stock) {
            if ($reste <= 0) break;

            $dispo = $stock->quantite;
            if ($dispo <= 0) continue;

            $prend = min($dispo, $reste);
            $lots[] = [
                'stock_id'        => $stock->id,
                'numero_lot'      => $stock->numero_lot,
                'quantite'        => $prend,
                'date_expiration' => $stock->date_expiration->format('d/m/Y')
            ];
            $reste -= $prend;
        }

        return $lots;
    }

    public function modifierQuantitePanier(Request $request, $index)
    {
        // dd(1);
        $validated = $request->validate([
            'quantite' => ['required', 'integer', 'min:1'],
        ]);

        $panier = session()->get('panier', []);

        if (!isset($panier[$index])) {
            return redirect()->back()
                ->with('error', 'Article introuvable dans le panier');
        }

        $medicamentId = $panier[$index]['medicament_id'];
        $med = Medicament::with([
            'stocks' => fn($q) => $q->where('is_actif', true)
                ->where('date_expiration', '>', now())
                ->where('quantite', '>', 0)
                ->orderBy('date_expiration', 'asc')
        ])->findOrFail($medicamentId);

        $stockTotal = $med->stocks->sum('quantite');
        if ($stockTotal < $validated['quantite']) {
            return redirect()->back()
                ->with('error', "Stock insuffisant (disponible : {$stockTotal})");
        }

        $lots = $this->calculerLots($med->stocks, $validated['quantite']);
        $prix = $med->stocks->first()->prix_vente;

        $panier[$index]['quantite']    = $validated['quantite'];
        $panier[$index]['total_ligne'] = $prix * $validated['quantite'];
        $panier[$index]['lots']        = $lots;

        session()->put('panier', $panier);

        return redirect()->back()
            ->with('success', 'Quantité mise à jour');
    }

    public function retirerDuPanier($index)
    {
        $panier = session()->get('panier', []);

        if (isset($panier[$index])) {
            unset($panier[$index]);
            $panier = array_values($panier);
            session()->put('panier', $panier);
        }

        return redirect()->back()
            ->with('success', 'Article supprimé du panier');
    }

    public function appliquerRemise(Request $request)
    {
        $validated = $request->validate([
            'type'   => ['required', 'in:montant,pourcentage'],
            'valeur' => ['required', 'numeric', 'min:0'],
        ]);

        $panier    = session()->get('panier', []);
        $sousTotal = collect($panier)->sum('total_ligne');

        $montant = $validated['valeur'];
        if ($validated['type'] === 'pourcentage') {
            $montant = $sousTotal * ($validated['valeur'] / 100);
        }

        session()->put('remise', [
            'type'    => $validated['type'],
            'valeur'  => $validated['valeur'],
            'montant' => $montant,
        ]);

        return redirect()->back()
            ->with('success', 'Remise appliquée : ' . number_format($montant, 2));
    }



    public function checkout()
    {
        // dd(1);
        $panier = session()->get('panier', []);
        if (empty($panier)) {
            return redirect()->route('ventes.index')
                ->with('error', 'Le panier est vide');
        }

        $missing = collect($panier)
            ->filter(fn($i) => $i['requiert_ordonnance'] && empty($i['ordonnance']))
            ->count();

        if ($missing > 0) {
            return redirect()->route('ventes.index')
                ->with('error', "Il manque {$missing} ordonnances. Vous devez les ajouter avant le paiement.");
        }

        $remise = session()->get('remise', ['montant' => 0]);
        $totalHT = collect($panier)->sum('total_ligne');
        $totalTTC =  $totalHT - $remise['montant'];

        return view('ventes.checkout', compact('panier', 'totalHT', 'totalTTC', 'remise'));
    }

    public function finaliserVente(Request $request)
    {
        $validated = $request->validate([
            'mode_paiement' => ['required', 'in:espece,carte,mobile'],
            'nom_client'    => ['nullable', 'string', 'max:255'],
        ]);

        $panier = session()->get('panier', []);
        if (empty($panier)) {
            return redirect()->route('ventes.index')
                ->with('error', 'Panier vide – impossible de finaliser la vente');
        }

        $remise = session()->get('remise', ['type' => null, 'valeur' => 0, 'montant' => 0]);
        $totalHT  = collect($panier)->sum('total_ligne');
        $totalTTC = $totalHT - $remise['montant'];
        // dd($totalTTC,$remise);

        $cheminOrdonnance = collect($panier)
            ->filter(fn($i) => !empty($i['ordonnance']))
            ->pluck('ordonnance')
            ->first();
        // dd($cheminOrdonnance);

        DB::beginTransaction();

        try {
            $vente = Vente::create([
                'user_id'            => auth()->id(),
                'nom_client'         => $validated['nom_client'],
                'mode_paiement'      => $validated['mode_paiement'],
                'remise_globale'     => $remise['type'] === 'montant' ? $remise['valeur'] : 0,
                'remise_pourcentage' => $remise['type'] === 'pourcentage' ? $remise['valeur'] : 0,
                'total_ht'           => $totalHT,
                'total_ttc'          => $totalTTC,
                'statut'             => 'terminee',
                'ordonnance'         => $cheminOrdonnance,
                'ordonnance_requise' => collect($panier)->contains('requiert_ordonnance', true),
            ]);
            foreach ($panier as $item) {
                foreach ($item['lots'] as $lot) {

                    $stock = Stock::findOrFail($lot['stock_id']);

                    $avant = $stock->quantite;
                    $stock->decrement('quantite', $lot['quantite']);
                    StockVente::create([
                        'vente_id'      => $vente->id,
                        'stock_id'      => $stock->id,
                        'medicament_id' => $item['medicament_id'],
                        'quantite'      => $lot['quantite'],
                        'prix_unitaire' => $item['prix_unitaire'],
                        'total'         => $item['prix_unitaire'] * $lot['quantite'],
                        'etre_remise'   => false,
                    ]);

                    MovementStock::create([
                        'stock_id'       => $stock->id,
                        'user_id'        => auth()->id(),
                        'type'           => 'sortie',
                        'quantite'       => $lot['quantite'],
                        'quantite_avant' => $avant,
                        'quantite_apres' => $stock->quantite,
                        'motif'          => "Vente #{$vente->id}",
                        'vente_id'       => $vente->id,
                    ]);
                }
            }

            DB::commit();

            session()->forget(['panier', 'remise']);

            return redirect()->route('ventes.recu', $vente->id)
                ->with('success', 'Vente enregistrée avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
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
