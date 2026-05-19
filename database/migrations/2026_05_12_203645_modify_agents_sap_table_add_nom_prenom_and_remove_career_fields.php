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
        Schema::table('agents_sap', function (Blueprint $table) {
            // Supprimer les champs qui existent déjà dans carriere
            $table->dropColumn(['categorie', 'niveau', 'degre', 'population']);
        });

        Schema::table('agents_sap', function (Blueprint $table) {
            // Ajouter le champ nom_prenom pour stocker le nom complet
            $table->string('nom_prenom', 200)->nullable()->after('matricule');
            // Rendre nom et prenom nullable
            $table->string('nom', 100)->nullable()->change();
            $table->string('prenom', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents_sap', function (Blueprint $table) {
            // Restaurer les champs supprimés
            $table->enum('categorie', ['Exécution', 'Maîtrise', 'Cadre', 'Hors cadre'])->nullable();
            $table->string('niveau', 10)->nullable();
            $table->string('degre', 10)->nullable();
            $table->enum('population', ['BO', 'autre'])->default('BO');
            // Supprimer nom_prenom
            $table->dropColumn('nom_prenom');
        });
    }
};
