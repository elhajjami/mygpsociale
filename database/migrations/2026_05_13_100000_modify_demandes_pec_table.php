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
        Schema::table('demandes_pec', function (Blueprint $table) {
            // Modifier beneficiaire_type pour accepter 'ayant_droit'
            $table->enum('beneficiaire_type', ['agent', 'conjoint', 'enfant', 'ayant_droit'])
                ->change();

            // Renommer type_demande en type_prestation (ou ajouter type_prestation)
            $table->enum('type_prestation', ['consultation', 'analyse', 'radiologie', 'medicament', 'chirurgie', 'autre'])
                ->nullable()
                ->after('type_demande');

            // Ajouter date_soin
            $table->date('date_soin')->nullable()->after('date_devis');

            // Ajouter description/diagnostic
            $table->text('description')->nullable()->after('observations');

            // Ajouter cree_par et validee_par si manquants
            if (!Schema::hasColumn('demandes_pec', 'cree_par')) {
                $table->foreignId('cree_par')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('demandes_pec', 'validee_par')) {
                $table->foreignId('validee_par')->nullable()->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_pec', function (Blueprint $table) {
            $table->enum('beneficiaire_type', ['agent', 'conjoint', 'enfant'])->change();
            $table->dropColumn(['type_prestation', 'date_soin', 'description']);
        });
    }
};
