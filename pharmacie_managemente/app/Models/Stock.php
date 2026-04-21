<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    //
    protected $fillable = [
        'medicament_id',
        'numero_lot',
        'quantite',
        'seuil_minimum',
        'prix_achat',
        'prix_vente',
        'date_expiration',
        'is_actif'
    ];

    protected $casts = [
        'date_expiration' => 'date',
        'is_actif' => 'boolean',
    ];

    public function medicament()
    {
        return $this->belongsTo(Medicament::class);
    }

    public function stockVentes()
    {
        return $this->hasMany(StockVente::class);
    }

    public function movementStocks()
    {
        return $this->hasMany(MovementStock::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function scopeEnRupture($query)
    {
        return $query->where('quantite', 0);
    }

    public function stockFaible($query)
    {
        return $query->whereColumn('quantite', '<=', 'seuil_minimum')
            ->where('quantite', '>', 0);
    }

    public function stockPerimes($query)
    {
        return $query->where('date_expiration', '<', now());
    }

    public function stockPerimeBientot($query)
    {
        return $query->whereBetween('date_expiration', [now(), now()->addDays(30)]);
    }

    public function ventes(){
        return $this->belongsToMany(Vente::class,'stock_ventes')
        ->withPivot('quantite', 'prix_unitaire', 'total', 'medicament_id', 'etre_remise')
        ->withTimestamps();
    }
}
