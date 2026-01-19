<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $guarded = [];

    public function client(){
        return $this->belongsTo(Client::class);
    }
    
    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function budgetItem(){
        return $this->hasMany(BudgetItem::class);
    }
}
