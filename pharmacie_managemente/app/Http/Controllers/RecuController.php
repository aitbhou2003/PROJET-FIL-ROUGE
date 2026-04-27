<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use Barryvdh\DomPDF\Facade\Pdf;

class RecuController extends Controller
{
    //

     public function consulterRecu(Vente $vente)
    {
        $vente->load(['stockVentes.medicament', 'stockVentes.stock', 'user']);
        return view('ventes.recu', compact('vente'));
    }

    public function recu(Vente $vente)
    {
        $pdf = Pdf::loadView('ventes.recu_pdf', compact('vente'));

        return $pdf->download('recu-vente-' . $vente->id . '.pdf');
    }
}
