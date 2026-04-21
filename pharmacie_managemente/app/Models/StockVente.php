<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockVente extends Model
{
    //

    protected $table = 'stock_ventes';

    protected $fillable = [
        'vente_id',
        'stock_id',
        'quantite',
        'prix_unitaire',
        'total',
        'etre_remise'
    ];


    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function medicament()
    {
        return $this->belongsTo(Medicament::class);
    }
}
