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
        Schema::table('ayant_droits', function (Blueprint $table) {
            // Supprimer les colonnes nom et prenom si elles existent
            if (Schema::hasColumn('ayant_droits', 'nom')) {
                $table->dropColumn('nom');
            }
            if (Schema::hasColumn('ayant_droits', 'prenom')) {
                $table->dropColumn('prenom');
            }

            // Ajouter la colonne nom_prenom si elle n'existe pas
            if (!Schema::hasColumn('ayant_droits', 'nom_prenom')) {
                $table->string('nom_prenom')->after('agent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ayant_droits', function (Blueprint $table) {
            $table->dropColumn('nom_prenom');
            $table->string('nom')->after('agent_id');
            $table->string('prenom')->nullable()->after('nom');
        });
    }
};
