<?php

// Modelo que representa un artículo de presupuesto en la base de datos
// Define la relación con el presupuesto al que pertenece

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
    // Relación: Un artículo pertenece a un presupuesto
{
    protected $guarded = [];
    
    public function budget(){
        return $this->belongsTo(Budget::class);
    }
}
