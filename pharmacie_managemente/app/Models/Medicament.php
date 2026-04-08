<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicament extends Model
{

    //
    protected $fillable = [
        'categorie_id',
        'nom',
        'code_barre',
        'description',
        'fabricant',
        'forme_dosage',
        'ordonnance_requise',
        'image'
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockTotal()
    {
        return $this->stocks()->where('is_actif', true)->sum('quantite');
    }
}
