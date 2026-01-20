<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Mail\BudgetCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $budgets = Budget::with(['client', 'budgetItem'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'budgets' => $budgets
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'budget_number' => 'nullable|integer',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:draft,sent,accepted,rejected',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate budget number automatically if not provided
            if (empty($validated['budget_number'])) {
                $lastBudget = Budget::where('user_id', $request->user()->id)
                    ->orderBy('budget_number', 'desc')
                    ->first();
                $validated['budget_number'] = $lastBudget ? $lastBudget->budget_number + 1 : 1;
            }

            // Create the budget
            $budget = Budget::create([
                'user_id' => $request->user()->id,
                'client_id' => $validated['client_id'],
                'budget_number' => $validated['budget_number'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'subtotal' => $validated['subtotal'],
                'tax' => $validated['tax'],
                'total' => $validated['total'],
            ]);

            // Create budget items
            foreach ($validated['items'] as $item) {
                BudgetItem::create([
                    'budget_id' => $budget->id,
                    'title' => $item['title'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            // Load the budget with relationships
            $budget->load(['client', 'budgetItem']);

            return response()->json([
                'status' => true,
                'message' => 'Budget created successfully',
                'budget' => $budget
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to create budget: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $budget = Budget::with(['client', 'budgetItem'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$budget) {
            return response()->json([
                'status' => false,
                'message' => 'Budget not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'budget' => $budget
        ]);
    }

    public function update(Request $request, $id)
    {
        $budget = Budget::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$budget) {
            return response()->json([
                'status' => false,
                'message' => 'Budget not found or unauthorized'
            ], 404);
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'budget_number' => 'required|integer',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:draft,sent,accepted,rejected',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Update the budget
            $budget->update([
                'client_id' => $validated['client_id'],
                'budget_number' => $validated['budget_number'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'subtotal' => $validated['subtotal'],
                'tax' => $validated['tax'],
                'total' => $validated['total'],
            ]);

            // Delete existing items
            BudgetItem::where('budget_id', $budget->id)->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                BudgetItem::create([
                    'budget_id' => $budget->id,
                    'title' => $item['title'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            // Load the budget with relationships
            $budget->load(['client', 'budgetItem']);

            return response()->json([
                'status' => true,
                'message' => 'Budget updated successfully',
                'budget' => $budget
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to update budget: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $budget = Budget::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$budget) {
            return response()->json([
                'status' => false,
                'message' => 'Budget not found or unauthorized'
            ], 404);
        }

        // Delete budget items first (cascade should handle this, but being explicit)
        BudgetItem::where('budget_id', $budget->id)->delete();
        
        $budget->delete();

        return response()->json([
            'status' => true,
            'message' => 'Budget deleted successfully'
        ]);
    }

    public function downloadPDF(Request $request, $id)
    {
        $budget = Budget::with(['client', 'budgetItem'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$budget) {
            return response()->json([
                'status' => false,
                'message' => 'Budget not found or unauthorized'
            ], 404);
        }

        // Return budget data as JSON for client-side PDF generation
        return response()->json([
            'status' => true,
            'budget' => $budget
        ]);
    }

    public function sendEmail(Request $request, $id)
    {
        $budget = Budget::with(['client', 'budgetItem'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$budget) {
            return response()->json([
                'status' => false,
                'message' => 'Budget not found or unauthorized'
            ], 404);
        }

        // Verificar si el cliente tiene email
        if (!$budget->client->email) {
            return response()->json([
                'status' => false,
                'message' => 'Client does not have an email address'
            ], 400);
        }

        try {
            // Get company settings and user
            $user = $request->user();
            $companySettings = [
                'name' => $user->company_name,
                'logo' => $user->company_logo ? Storage::url($user->company_logo) : null,
                'phone' => $user->company_phone,
                'email' => $user->company_email,
                'address' => $user->company_address,
                'website' => $user->company_website,
            ];

            Log::info("Attempting to send budget #{$budget->budget_number} email to: {$budget->client->email}");
            Log::info("Company settings: " . json_encode($companySettings));

            // Enviar email al cliente con copia al usuario
            Mail::to($budget->client->email)
                ->cc($user->email)
                ->send(new BudgetCreated($budget, $companySettings));

            Log::info("Budget #{$budget->budget_number} email sent successfully to: {$budget->client->email} with CC to: {$user->email}");

            return response()->json([
                'status' => true,
                'message' => 'Email enviado exitosamente a ' . $budget->client->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send budget email: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => false,
                'message' => 'Error al enviar email: ' . $e->getMessage()
            ], 500);
        }
    }
}
