<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommandeController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        return view('commandes.index');
    }

    public function indexApi()
    {
        $commandes = Commande::orderBy('created_at', 'desc')->get();
        return response()->json($commandes);
    }

    public function store(Request $request): JsonResponse
    {
        $clientId = $request->input('client_id');
        $clientNom = $request->input('client_nom');
        $clientEmail = $request->input('client_email');

        // Vérifier que l'ID client est fourni
        if (!$clientId) {
            return response()->json([
                'error' => 'client_id est requis'
            ], 400);
        }

        // Récupérer le client depuis l'API
        $client = $this->clientService->getClient($clientId);

        // Vérifier que le client existe
        if (!$client) {
            return response()->json([
                'error' => 'Client ID ' . $clientId . ' n\'existe pas dans la base de données clients.'
            ], 400);
        }

        // Vérifier que le nom correspond
        if ($client['nom'] !== $clientNom) {
            return response()->json([
                'error' => 'Le nom du client ne correspond pas. Attendu: "' . $client['nom'] . '", Reçu: "' . $clientNom . '"'
            ], 400);
        }

        // Vérifier que l'email correspond
        if ($client['email'] !== $clientEmail) {
            return response()->json([
                'error' => 'L\'email du client ne correspond pas. Attendu: "' . $client['email'] . '", Reçu: "' . $clientEmail . '"'
            ], 400);
        }

        $validated = $request->validate([
            'client_id' => 'required|integer',
            'client_nom' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'produit' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,confirmee,expediee,livree,annulee',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $commande = Commande::create($validated);

        return response()->json([
            'commande' => $commande,
            'client' => $client
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $commande = Commande::findOrFail($id);
        return response()->json($commande);
    }

    public function update(Request $request, $id): JsonResponse
{
    $commande = Commande::findOrFail($id);

    // Ne vérifier que si les champs client sont modifiés
    $clientId = $request->input('client_id', $commande->client_id);
    $clientNom = $request->input('client_nom', $commande->client_nom);
    $clientEmail = $request->input('client_email', $commande->client_email);

    // Vérifier si l'ID client a changé ou si les infos doivent être revérifiées
    $clientIdChanged = $request->has('client_id') && $request->input('client_id') != $commande->client_id;
    $clientNomChanged = $request->has('client_nom') && $request->input('client_nom') != $commande->client_nom;
    $clientEmailChanged = $request->has('client_email') && $request->input('client_email') != $commande->client_email;

    // Si un des champs client est modifié, on vérifie la cohérence
    if ($clientIdChanged || $clientNomChanged || $clientEmailChanged) {
        // Vérifier que l'ID client est valide
        if (!$clientId) {
            return response()->json([
                'error' => 'client_id est requis lors de la modification du client'
            ], 400);
        }

        // Récupérer le client depuis l'API
        $client = $this->clientService->getClient($clientId);

        // Vérifier que le client existe
        if (!$client) {
            return response()->json([
                'error' => 'Client ID ' . $clientId . ' n\'existe pas dans la base de données clients.'
            ], 400);
        }

        // Vérifier que le nom correspond
        if ($client['nom'] !== $clientNom) {
            return response()->json([
                'error' => 'Le nom du client ne correspond pas. Attendu: "' . $client['nom'] . '", Reçu: "' . $clientNom . '"'
            ], 400);
        }

        // Vérifier que l'email correspond
        if ($client['email'] !== $clientEmail) {
            return response()->json([
                'error' => 'L\'email du client ne correspond pas. Attendu: "' . $client['email'] . '", Reçu: "' . $clientEmail . '"'
            ], 400);
        }
    }

    $validated = $request->validate([
        'client_id' => 'sometimes|integer',
        'client_nom' => 'sometimes|string|max:255',
        'client_email' => 'sometimes|email|max:255',
        'produit' => 'sometimes|string|max:255',
        'montant' => 'sometimes|numeric|min:0',
        'statut' => 'sometimes|in:en_attente,confirmee,expediee,livree,annulee',
        'date_commande' => 'sometimes|date',
        'date_livraison_prevue' => 'nullable|date',
        'notes' => 'nullable|string'
    ]);

    $commande->update($validated);
    return response()->json($commande);
}

    public function destroy($id): JsonResponse
    {
        $commande = Commande::findOrFail($id);
        $commande->delete();
        return response()->json(null, 204);
    }
}
