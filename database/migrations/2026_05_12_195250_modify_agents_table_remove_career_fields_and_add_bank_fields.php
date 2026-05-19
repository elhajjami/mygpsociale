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
        Schema::table('agents', function (Blueprint $table) {
            // Supprimer les champs qui existent déjà dans carriere
            $table->dropColumn(['categorie', 'categorie_calculee', 'niveau', 'degre']);
            // Supprimer population (traité comme BO par défaut)
            $table->dropColumn('population');
        });

        Schema::table('agents', function (Blueprint $table) {
            // Ajouter les champs bancaires
            $table->string('compte_bancaire', 50)->nullable()->after('date_affiliation');
            $table->string('cle_bancaire', 20)->nullable()->after('compte_bancaire');
            $table->string('banque', 100)->nullable()->after('cle_bancaire');
            $table->text('info_banque')->nullable()->after('banque');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Restaurer les champs supprimés
            $table->enum('categorie', ['Exécution', 'Maîtrise', 'Cadre', 'Hors cadre'])->nullable();
            $table->string('categorie_calculee', 50)->nullable();
            $table->string('niveau', 10)->nullable();
            $table->string('degre', 10)->nullable();
            $table->enum('population', ['BO', 'autre'])->default('BO');
        });

        Schema::table('agents', function (Blueprint $table) {
            // Supprimer les champs bancaires
            $table->dropColumn(['info_banque', 'banque', 'cle_bancaire', 'compte_bancaire']);
        });
    }
};
