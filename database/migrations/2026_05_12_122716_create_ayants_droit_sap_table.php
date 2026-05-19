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
        Schema::create('ayants_droit_sap', function (Blueprint $table) {
            $table->id();
            $table->string('matricule_agent', 20); // Référence par matricule, pas par ID
            $table->enum('type', ['conjoint', 'enfant']);
            $table->string('nom_prenom', 200);
            $table->date('date_naissance')->nullable();
            $table->string('cin', 20)->nullable();
            $table->enum('statut', ['Validé', 'En attente', 'Rejeté', 'Inactif'])->default('En attente');
            $table->text('observations')->nullable();

            // Métadonnées d'import
            $table->timestamp('date_import_sap')->nullable();
            $table->string('fichier_import', 255)->nullable();
            $table->unsignedBigInteger('import_par')->nullable();

            $table->timestamps();

            // Index pour la recherche
            $table->index('matricule_agent');
            $table->index('type');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayants_droit_sap');
    }
};
