<?php

namespace App\Http\Controllers;

use App\Models\MovementStock;
use App\Models\Stock;
use App\Models\StockVente;
use App\Models\Vente;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    //

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
            // dd($vente);
            return redirect()->route('ventes.consulterRecu', $vente->id)
                ->with('success', 'Vente enregistrée avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

}

