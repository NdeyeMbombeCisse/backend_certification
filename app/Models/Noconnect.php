<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noconnect extends Model
{
    use HasFactory;
    protected $table = 'no_connects';

    protected $guarded=[];
    public function reservations(){
        return $this->hasmany(Reservation::class);
    }
}
