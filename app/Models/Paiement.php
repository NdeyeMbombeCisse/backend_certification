<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }
}
// naboo-41628b36-c3c3-4f9f-b7cc-2605f6e2adc3.5a109426-0a1c-49b5-ada6-f32c88c39fd2