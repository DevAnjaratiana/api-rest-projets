<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_nom',
        'client_email',
        'produit',
        'montant',
        'statut',
        'date_commande',
        'date_livraison_prevue',
        'notes'
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison_prevue' => 'date',
        'montant' => 'decimal:2'
    ];
}
