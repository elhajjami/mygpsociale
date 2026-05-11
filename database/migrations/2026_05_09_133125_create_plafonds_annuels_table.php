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
        Schema::create('plafonds_annuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->year('annee');
            $table->decimal('plafond_annuel', 10, 2)->default(0);
            $table->decimal('montant_consome', 10, 2)->default(0);
            $table->decimal('montant_engage', 10, 2)->default(0);
            $table->decimal('reste_disponible', 10, 2)->default(0);
            $table->timestamps();

            // Unique constraint pour empêcher les doublons agent/année
            $table->unique(['agent_id', 'annee']);

            // Index
            $table->index('agent_id');
            $table->index('annee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plafonds_annuels');
    }
};
