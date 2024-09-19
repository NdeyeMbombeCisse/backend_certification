<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $guarded=[];
    public function categorie(){
        return $this->belongsto(Categorie::class);
    }

    public function bateau(){
        return $this->belongsto(Bateau::class);
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }



}
