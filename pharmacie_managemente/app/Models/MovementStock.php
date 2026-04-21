<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementStock extends Model
{
    //
    protected $table = 'movement_stocks';

    protected $fillable = [
        'stock_id',
        'user_id',
        'type',
        'quantite',
        'quantite_avant',
        'quantite_apres',
        'motif',
        'vente_id'
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }
    
    
}
