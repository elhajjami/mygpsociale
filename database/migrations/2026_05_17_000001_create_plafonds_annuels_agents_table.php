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
        Schema::create('plafonds_annuels_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->integer('annee')->comment('Année civile');

            // Plafond annuel de l'agent
            $table->decimal('plafond_annuel', 10, 2)->default(12000)->comment('Plafond annuel total de l\'agent');

            // Montants consommés
            $table->decimal('consomme_medical', 10, 2)->default(0)->comment('Montant consommé en formation médicale (montant total)');
            $table->decimal('consomme_clinique', 10, 2)->default(0)->comment('Montant consommé en hospitalisation (part adhérent seulement)');

            // Restes disponibles
            $table->decimal('reste_medical', 10, 2)->default(12000)->comment('Reste disponible pour formation médicale');
            $table->decimal('reste_clinique', 10, 2)->default(12000)->comment('Reste disponible pour hospitalisation');

            $table->timestamps();

            // Index unique pour éviter les doublons agent/année
            $table->unique(['agent_id', 'annee']);
            $table->index('annee');
            $table->index(['agent_id', 'annee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plafonds_annuels_agents');
    }
};
