<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter les nouvelles colonnes uniques
        Schema::table('plafonds_annuels_agents', function (Blueprint $table) {
            $table->decimal('consomme', 10, 2)->default(0)->after('plafond_annuel');
            $table->decimal('reste', 10, 2)->default(0)->after('consomme');
        });

        // Migrer les données existantes
        // Pour chaque enregistrement, consomme = consomme_medical + consomme_clinique
        // Et reste = plafond_annuel - consomme
        DB::statement('
            UPDATE plafonds_annuels_agents
            SET consomme = COALESCE(consomme_medical, 0) + COALESCE(consomme_clinique, 0),
                reste = plafond_annuel - (COALESCE(consomme_medical, 0) + COALESCE(consomme_clinique, 0))
        ');

        // Supprimer les anciennes colonnes
        Schema::table('plafonds_annuels_agents', function (Blueprint $table) {
            $table->dropColumn(['consomme_medical', 'consomme_clinique', 'reste_medical', 'reste_clinique']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer les anciennes colonnes
        Schema::table('plafonds_annuels_agents', function (Blueprint $table) {
            $table->decimal('consomme_medical', 10, 2)->default(0)->after('plafond_annuel');
            $table->decimal('consomme_clinique', 10, 2)->default(0)->after('consomme_medical');
            $table->decimal('reste_medical', 10, 2)->default(0)->after('consomme_clinique');
            $table->decimal('reste_clinique', 10, 2)->default(0)->after('reste_medical');
        });

        // Restaurer les données (consomme -> consomme_medical, reste -> reste_medical)
        DB::statement('
            UPDATE plafonds_annuels_agents
            SET consomme_medical = COALESCE(consomme, 0),
                reste_medical = COALESCE(reste, 0),
                consomme_clinique = 0,
                reste_clinique = COALESCE(reste, 0)
        ');

        // Supprimer les nouvelles colonnes
        Schema::table('plafonds_annuels_agents', function (Blueprint $table) {
            $table->dropColumn(['consomme', 'reste']);
        });
    }
};
