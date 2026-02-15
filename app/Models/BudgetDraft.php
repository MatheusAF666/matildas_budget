<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetDraft extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'saved_at' => 'datetime',
    ];
}
