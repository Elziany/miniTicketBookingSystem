<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['reservation_id', 'timeframe', 'sent_at'])]
class ReservationReminders extends Model
{
    public function reservation(){
        return $this->belongsTo(Reservation::class , 'reservation_id');
    }
}
