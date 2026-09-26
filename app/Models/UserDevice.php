<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'platform', 'device_token'])]
class UserDevice extends Model
{
   public function user(){
     return  $this->belongsTo(User::class , 'user_id');
   }
}
