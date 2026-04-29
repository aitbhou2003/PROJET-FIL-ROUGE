<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Stock;

class AlerteController extends Controller
{
    //
    public function index()
    {
        $alertes = Notification::with('stock.medicament')
            ->orderByDesc('created_at')
            ->paginate(20);

        $nonLues = Notification::where('is_read', false)->count();

        return view('admin.alertes.index', compact('alertes', 'nonLues'));
    }


    public function dernieresAlertes()
    {
        $alertes = Notification::with('stock.medicament')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $nonLues = Notification::where('is_read', false)->count();

        return response()->json([
            'alertes' => $alertes,
            'non_lues' => $nonLues
        ]);
    }

    public function marquerCommeLue($id)
    {
        $alerte = Notification::findOrFail($id);
        $alerte->update(['is_read' => true]);

        return redirect()->back()
            ->with('success', 'Alerte marquée comme lue');
    }

    public function toutMarquerCommeLu()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        return redirect()->back()
            ->with('success', 'Toutes les alertes ont été marquées comme lues');
    }


    public function genererAlertes()
    {
        $count = 0;

        $stocksFaibles = Stock::with('medicament')
            ->whereColumn('quantite', '<=', 'seuil_minimum')
            ->where('is_actif', true)
            ->get();

        foreach ($stocksFaibles as $stock) {
            $existe = Notification::where('stock_id', $stock->id)
                ->where('type', 'stock_faible')
                ->where('is_read', false)
                ->exists();

            if (!$existe) {
                Notification::create([
                    'stock_id' => $stock->id,
                    'type' => 'stock_faible',
                    'message' => "Le stock de {$stock->medicament->nom} (Lot: {$stock->numero_lot}) est inférieur au seuil minimum ({$stock->quantite} / {$stock->seuil_minimum})",
                    'is_read' => false,
                    'is_sent' => false,
                ]);
                $count++;
            }
        }

        $stocksPeremption = Stock::with('medicament')
            ->where('date_expiration', '<=', now()->addDays(30))
            ->where('quantite', '>', 0)
            ->where('is_actif', true)
            ->get();

        foreach ($stocksPeremption as $stock) {
            $existe = Notification::where('stock_id', $stock->id)
                ->where('type', 'peremption')
                ->where('is_read', false)
                ->exists();

            if (!$existe) {
                $joursRestants = now()->diffInDays($stock->date_expiration, false);
                Notification::create([
                    'stock_id' => $stock->id,
                    'type' => 'peremption',
                    'message' => "Le lot {$stock->numero_lot} de {$stock->medicament->nom} expire dans {$joursRestants} jours ({$stock->date_expiration->format('d/m/Y')})",
                    'is_read' => false,
                    'is_sent' => false,
                ]);
                $count++;
            }
        }

        return redirect()->back()
            ->with('success', "{$count} nouvelles alertes générées");
    }


}
