<?php

namespace App\Models;
// Modelo que representa un cliente en la base de datos
// Define las relaciones con presupuestos y usuario

use Illuminate\Database\Eloquent\Model;

class Client extends Model
    // Relación: Un cliente puede tener muchos presupuestos
{
    protected $guarded = [];

    public function Budget(){
            // Relación: Un cliente pertenece a un usuario
        return $this->hasMany(Budget::class);
    }
    public function User(){
        return $this->belongsTo(User::class);
    }
}
