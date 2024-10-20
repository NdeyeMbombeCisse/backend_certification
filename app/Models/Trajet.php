<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function bateau(){
        return $this->belongsTo(Bateau::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'reservations');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }


    public function places()
    {
        return $this->hasManyThrough(Place::class, Bateau::class, 'id', 'bateau_id', 'bateau_id', 'id');
    }
}

