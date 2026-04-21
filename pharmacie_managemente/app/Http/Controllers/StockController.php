<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\MovementStock;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $stocks = Stock::with('medicament')
            ->where('is_actif', true)
            ->orderBy('date_expiration')
            ->paginate('15');

        return view('stocks.index', compact('stocks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id = null)
    {
        //
        $medicaments = Medicament::all();
        $medicament = Medicament::find($id);

        return view('stocks.create', compact('medicaments', 'medicament'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'medicament_id' => ['required', 'exists:medicaments,id'],
            'numero_lot' =>  ['required', 'string'],
            'quantite' => ['required', 'integer', 'min:1'],
            'seuil_minimum' => ['required', 'integer', 'min:1'],
            'prix_achat' => ['required', 'numeric', 'min:0'],
            'prix_vente' => ['required', 'numeric', 'min:0'],
            'date_expiration' => ['required', 'date', 'after:today'],
        ]);

        $stock = Stock::create([
            'medicament_id' => $validated['medicament_id'],
            'numero_lot' => $validated['numero_lot'],
            'quantite' => $validated['quantite'],
            'seuil_minimum' => $validated['seuil_minimum'],
            'prix_achat' => $validated['prix_achat'],
            'prix_vente' => $validated['prix_vente'],
            'date_expiration' => $validated['date_expiration'],
            'is_actif' => true
        ]);
        MovementStock::create([
            'stock_id' => $stock->id,
            'user_id' => auth()->id(),
            'type' => 'entree',
            'quantite' => $validated['quantite'],
            'quantite_avant' => 0,
            'quantite_apres' => $validated['quantite'],
            'motif' => 'Réception nouvelle commande',
        ]);

        return redirect()->back()
            ->with('success', 'Stock ajouté avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $stock = Stock::with(['medicament', 'movementStocks.user'])->find($id);
        return view('stocks.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $stock = Stock::with('medicament')->findOrFail($id);
        return view('stocks.edit', compact('stock'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        //
        $validated = $request->validate([
            'quantite_ajustee' => ['required', 'numeric', 'min:0'],
            'motif' => ['required', 'string'],
        ]);

        $ancienne = $stock->quantite;
        $nouvelle =  $validated['quantite_ajustee'];

        $stock->update(['quantite' => $nouvelle]);
        $type = 'sortie';
        if ($nouvelle > $ancienne) {
            $type = 'entree';
        }

        MovementStock::create([
            'stock_id' => $stock->id,
            'user_id' => auth()->id(),
            'type' => 'ajustement',
            'quantite' => abs($nouvelle - $ancienne),
            'quantite_avant' => $ancienne,
            'quantite_apres' => $nouvelle,
            'motif' => $validated['motif'],
        ]);

        return redirect()->back()
            ->with('success', 'Stock ajusté');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
