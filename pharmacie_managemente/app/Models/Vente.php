<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    //
    protected $fillable = [
        'user_id',
        'nom_client',
        'telephone_client',
        'mode_paiement',
        'remise',
        'total'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockVentes()
    {
        return $this->hasMany(StockVente::class);
    }

    public function calculerTotal()
    {
        $this->total = $this->stockVentes->sum('total') - $this->remise;
        return $this;
    }
}
