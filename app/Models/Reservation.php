<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    public function place()
    {
        return $this->hasOne(Place::class);
    }

    
}
