<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bateau extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $attributes = [
        'statut' => 0, // Définit 0 comme valeur par défaut
    ];

    public function trajets(){
        return $this->hasmany(Trajet::class);
    }

    public function places(){
        return $this->hasmany(Place::class);
    }





}
