<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClientService
{
    protected $baseUrl = 'http://localhost:8001/api';

    public function getClient($id)
    {
        $response = Http::get("{$this->baseUrl}/clients/{$id}");
        return $response->successful() ? $response->json() : null;
    }

    public function getAllClients()
    {
        $response = Http::get("{$this->baseUrl}/clients");
        return $response->successful() ? $response->json() : [];
    }

    public function clientExists($id)
    {
        return $this->getClient($id) !== null;
    }
}
