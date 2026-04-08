<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    protected $fillable = [
        'stock_id',
        'type',
        'message',
        'is_read',
        'is_sent'
    ];


    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

   
}
