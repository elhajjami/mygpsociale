<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Situation administrative (codes de mesure: DE, SU, SC, etc.)
            $table->string('situation_administrative', 50)->nullable()->after('statut');

            // Catégorie calculée depuis le niveau (Carrière)
            $table->string('categorie_calculee', 50)->nullable()->after('categorie');

            // Statut calculé depuis la situation (Mesure)
            $table->string('statut_calcule', 50)->nullable()->after('statut');

            // Index pour la recherche
            $table->index('situation_administrative');
            $table->index('categorie_calculee');
            $table->index('statut_calcule');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropIndex(['situation_administrative']);
            $table->dropIndex(['categorie_calculee']);
            $table->dropIndex(['statut_calcule']);

            $table->dropColumn(['situation_administrative', 'categorie_calculee', 'statut_calcule']);
        });
    }
};
