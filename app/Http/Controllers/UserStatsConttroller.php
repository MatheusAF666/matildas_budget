<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Budget;

class UserStatsConttroller extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::where('user_id', $request->user()->id)
            ->count();
        $budgets = Budget::where('user_id', $request->user()->id)
            ->count();
        
        $lastBudget = Budget::where('user_id', $request->user()->id)
            ->with('client')
            ->latest()
            ->first();

        return response()->json([
            'status' => true,
            'clients' => $clients,
            'budgets' => $budgets,
            'lastBudget' => $lastBudget ? [
                'budget_number' => $lastBudget->budget_number,
                'client_name' => $lastBudget->client->name ?? 'N/A',
                'created_at' => $lastBudget->created_at->format('d/m/Y'),
                'total' => number_format($lastBudget->total, 2)
            ] : null
        ]);
    }
}
