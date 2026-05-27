<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index()
    {
        return view('clients.index');
    }

    public function indexApi(): JsonResponse
    {
        $clients = Client::orderBy('created_at', 'desc')->get();
        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients|max:255',
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        $client = Client::create($validated);
        return response()->json($client, 201);
    }

    public function show($id): JsonResponse
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:clients,email,' . $id . '|max:255',
            'telephone' => 'sometimes|string|max:20',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        $client->update($validated);
        return response()->json($client);
    }

    public function destroy($id): JsonResponse
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(null, 204);
    }
}
