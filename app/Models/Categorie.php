<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;
    protected $guarded=[];
    
    public function places(){
        return $this->hasmany(Place::class);
    }

  

    public function tarifs() {
        return $this->hasMany(Tarif::class);
    }

}
