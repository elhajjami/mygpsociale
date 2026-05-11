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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 20)->unique();
            $table->string('nom', 100);
            $table->string('prenom', 100)->nullable();
            $table->string('cin', 20)->nullable();
            $table->date('date_naissance')->nullable();
            $table->enum('categorie', ['Exécution', 'Maîtrise', 'Cadre', 'Hors cadre']);
            $table->string('niveau', 10)->nullable();
            $table->string('degre', 10)->nullable();
            $table->string('dp_affectation', 100)->nullable();
            $table->enum('population', ['BO', 'autre'])->default('autre');
            $table->enum('statut', ['Actif', 'Retraité', 'Sorti', 'Décédé', 'Suspendu', 'Supprimé'])->default('Actif');
            $table->date('date_entree')->nullable();
            $table->date('date_sortie')->nullable();
            $table->date('date_retraite')->nullable();
            $table->string('numero_immatriculation', 50)->nullable();
            $table->string('numero_affiliation', 50)->nullable();
            $table->text('observations')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Index pour la recherche
            $table->index('matricule');
            $table->index('statut');
            $table->index('categorie');
            $table->index('dp_affectation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
