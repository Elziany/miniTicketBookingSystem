<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable('user_id','hall_id' , 'is_manager')]
#[Table('hall_agent')]
class HallAgent extends Model
{
    public $timestamps = false;
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }   

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }
}
