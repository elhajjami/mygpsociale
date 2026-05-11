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
        Schema::create('ayants_droit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['conjoint', 'enfant']);
            $table->string('nom_prenom', 200);
            $table->date('date_naissance')->nullable();
            $table->string('cin', 20)->nullable();
            $table->enum('statut', ['Validé', 'En attente', 'Rejeté', 'Inactif'])->default('En attente');
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('agent_id');
            $table->index('type');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayants_droit');
    }
};
