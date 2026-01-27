<?php

// Modelo que representa un presupuesto en la base de datos
// Define las relaciones con cliente, usuario y artículos

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
    // Relación: Un presupuesto pertenece a un cliente
{
    protected $guarded = [];

    public function client(){
            // Relación: Un presupuesto pertenece a un usuario
        return $this->belongsTo(Client::class);
    }
    
    public function user(){
            // Relación: Un presupuesto tiene muchos artículos
        return $this->belongsTo(User::class);
    }
    
    public function budgetItem(){
        return $this->hasMany(BudgetItem::class);
    }
}
