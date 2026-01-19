<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'clients' => $clients
        ]);
    }

    public function show(Request $request, $id)
    {
        $client = Client::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Client not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'client' => $client
        ]);
    }

    public function storeClient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'floor' => 'nullable|string|max:20',
            'door' => 'nullable|string|max:20',
            'city' => 'required|string|max:100',
        ]);

        // Add the authenticated user's ID to the client data
        $validated['user_id'] = $request->user()->id;

        $client = Client::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Client created successfully',
            'client' => $client
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $client = Client::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Client not found or unauthorized'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'floor' => 'nullable|string|max:20',
            'door' => 'nullable|string|max:20',
            'city' => 'required|string|max:100',
        ]);

        $client->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Client updated successfully',
            'client' => $client
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $client = Client::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Client not found or unauthorized'
            ], 404);
        }

        $client->delete();

        return response()->json([
            'status' => true,
            'message' => 'Client deleted successfully'
        ]);
    }
}