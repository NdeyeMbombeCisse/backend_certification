<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function bateau(){
        return $this->belongsto(Bateau::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'reservations');
    }
}

