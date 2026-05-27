<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('client_nom');
            $table->string('client_email');
            $table->string('produit');
            $table->decimal('montant', 10, 2);
            $table->enum('statut', ['en_attente', 'confirmee', 'expediee', 'livree', 'annulee'])->default('en_attente');
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
