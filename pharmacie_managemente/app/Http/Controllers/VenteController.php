<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Medicament;
use App\Models\Vente;
use Illuminate\Http\Request;

use function Symfony\Component\Clock\now;

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        //test
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

    }

    /**
     * Display the specified resource.
     */
    public function show(Vente $vente)
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
    public function destroy(Vente $vente)
    {
        //

    }
}
