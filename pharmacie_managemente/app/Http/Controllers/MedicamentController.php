<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicamentRequest;
use App\Models\Categorie;
use App\Models\Medicament;
use App\Models\MovementStock;
use App\Models\Stock;
use Illuminate\Http\Request;
// use SebastianBergmann\CodeCoverage\Test\TestSize\Medium;

class MedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        //  $search = $request->search ;
        // \dd($search);


        $medicaments = Medicament::with(['categorie', 'stocks' => function ($q) {
            $q->where('is_actif', true);
        }]);
        if ($request->filled('search')) {
            $medicaments->where('nom', 'like', '%' . $request->search . '%')
                ->orWhere('code_barre', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('categorie')) {
            $medicaments->where('categorie_id', $request->categorie);
        }

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
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('medicaments', 'public');
        }


        $medicament = Medicament::create([
            'categorie_id' => $validated['categorie_id'],
            'nom' => $validated['nom'],
            'code_barre' => $validated['code_barre'],
            'description' => $validated['description'],
            'fabricant' => $validated['fabricant'],
            'forme_dosage' => $validated['forme_dosage'],
            'image' => $imagePath ?? null,
            'ordonnance_requise' => $request->boolean('ordonnance_requise'),
        ]);
        $stock=Stock::create([
            'medicament_id' => $medicament->id,
            'numero_lot' => $validated['numero_lot'],
            'quantite' => $validated['quantite'],
            'seuil_minimum' => $validated['seuil_minimum'],
            'prix_achat' => $validated['prix_achat'],
            'prix_vente' => $validated['prix_vente'],
            'date_expiration' => $validated['date_expiration'],
            'is_actif' => true,
        ]);

        MovementStock::created([
            'stock_id'=>$stock->id,
            'user_id'=>auth()->id(),
            'type'=>'entree',
            'quantite'=>$validated['quantite'],
            'quantite_avant'=>0,
            'quantite_apres'=>$validated['quantite'],
            'motif'=> 'Création médicament et stock initial',
        ]);

        return redirect()->route('medicaments.index')
            ->with('success', 'Médicament ajouté avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicament $medicament)
    {
        //
        $medicament->load(['stocks' => function ($q) {
            $q->where('is_actif', \true)
                ->orderBy('date_expiration', 'asc');
        }, 'stocks', 'categorie']);
        // \dd($medicament->categorie->nom);
        foreach ($medicament->stocks as $stocks) {
            echo '<pre>';
            var_dump($stocks);
            echo '</pre>';
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medicament $medicament)
    {
        //
        $categories = Categorie::all();
        return view('medicaments.edit', compact('medicament', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicament $medicament)
    {
        //
        $validated = $request->validate([
            'categorie_id' => ['required', 'exists:categories,id'],
            'nom' => ['required', 'string', 'max:255'],
            'code_barre' => ['required', 'string', 'unique:medicaments'],
            'description' => ['nullable', 'string'],
            'fabricant' => ['required', 'string'],
            'forme_dosage' => ['required', 'string'],
            'ordonnance_requise' => 'boolean',
        ]);

        $medicament->update($validated);

        return redirect()->route('medicaments.index')
            ->with('success', 'Médicament est modifiee');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicament $medicament)

    {
        //
        // \dd($medicament);
        $medicament->delete();
        return redirect()->route('medicaments.index')
            ->with('success', 'Médicament désactivé.');
    }
}
