<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;

class PanierController extends Controller
{
    //
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

        if ($montant > $sousTotal) {
            return redirect()->back()
                ->with('error', 'La remise ne peut pas dépasser le montant total de la vente.');
        }

        session()->put('remise', [
            'type'    => $validated['type'],
            'valeur'  => $validated['valeur'],
            'montant' => $montant,
        ]);

        return redirect()->back()
            ->with('success', 'Remise appliquée : ' . number_format($montant, 2));
    }

    public function supprimerRemise()
    {
        session()->forget('remise');

        return redirect()->route('ventes.checkout')
            ->with('success', 'Remise supprimée');
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
}
