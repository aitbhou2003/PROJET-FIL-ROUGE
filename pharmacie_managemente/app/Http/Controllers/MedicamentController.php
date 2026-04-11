<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicamentRequest;
use App\Models\Categorie;
use App\Models\Medicament;
use App\Models\Stock;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Test\TestSize\Medium;

class MedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $medicaments = Medicament::with('categorie', 'stocks');
        $medicaments = $medicaments->latest()->paginate(10);
        $categories = Categorie::all();
        return view('medicaments.index', compact('medicaments', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Categorie::all();
        return view('medicaments.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicamentRequest $request)
    {
        //
        $validated = $request->validated();


        $medicament = Medicament::create([
            'categorie_id' => $validated['categorie_id'],
            'nom' => $validated['nom'],
            'code_barre' => $validated['code_barre'],
            'description' => $validated['description'],
            'fabricant' => $validated['fabricant'],
            'forme_dosage' => $validated['forme_dosage'],
            'ordonnance_requise' => $request->boolean('ordonnance_requise'),
        ]);

        Stock::create([
            'medicament_id' => $medicament->id,
            'numero_lot' => $validated['numero_lot'],
            'quantite' => $validated['quantite'],
            'seuil_minimum' => $validated['seuil_minimum'],
            'prix_achat' => $validated['prix_achat'],
            'prix_vente' => $validated['prix_vente'],
            'date_expiration' => $validated['date_expiration'],
            'is_actif' => true,
        ]);

        return redirect()->route('medicaments.index')
            ->with('success', 'Médicament ajouté avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
