<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    public function Budget(){
        return $this->hasMany(Budget::class);
    }
    public function User(){
        return $this->belongsTo(User::class);
    }
}
