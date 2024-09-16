<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bateau extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function trajets(){
        return $this->hasmany(Trajet::class);
    }

    public function places(){
        return $this->hasmany(Place::class);
    }





}
