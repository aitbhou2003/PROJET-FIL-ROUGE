<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    //
    protected $fillable = [
        'user_id',
        'nom_client',
        'mode_paiement',
        'remise_globale',
        'total',
        'remise_pourcentage',
        'total_ht',
        'total_ttc',
        'statut',
        'date_annulation',
        'motif_annulation',
        'date_annulation',
        'ordonnance',

    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockVentes()
    {
        return $this->hasMany(StockVente::class);
    }



    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'stock_ventes')
            ->withPivot('quantite', 'prix_unitaire', 'total', 'medicament_id', 'etre_remise')
            ->withTimestamps();
    }

    public function mouvementStocks()
    {
        return $this->hasMany(MovementStock::class);
    }

    public function isAnulle(): bool
    {
        return $this->statue === 'anuulee';
    }

    public function isTerminee(): bool
    {
        return $this->statut === 'terminee';
    }
}
