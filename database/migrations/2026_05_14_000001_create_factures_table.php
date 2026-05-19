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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();

            // Type de facture
            $table->enum('type_facture', ['medical', 'clinique'])->default('medical');

            // Relation avec DemandePEC (optionnel)
            $table->foreignId('demande_pec_id')->nullable()->constrained('demandes_pec')->onDelete('set null');

            // Partenaire (prestataire)
            $table->foreignId('partenaire_id')->nullable()->constrained()->onDelete('set null');

            // Informations facture
            $table->date('date_facture');
            $table->date('date_echeance')->nullable();

            // Informations clinique (si type clinique)
            $table->string('nom_patient')->nullable();
            $table->date('hospitalisation_du')->nullable();
            $table->date('hospitalisation_au')->nullable();

            // Montants
            $table->decimal('montant_ht', 10, 2)->default(0);
            $table->decimal('montant_ttc', 10, 2)->default(0);
            $table->decimal('montant_clinique', 10, 2)->default(0)->comment('Total prestations clinique');
            $table->decimal('montant_honoraires', 10, 2)->default(0)->comment('Total honoraires médecins');
            $table->decimal('montant_autres', 10, 2)->default(0)->comment('Total autres prestations');

            // Statut
            $table->enum('statut', ['brouillon', 'generee', 'envoyee', 'payee', 'annulee'])->default('brouillon');

            // Observations
            $table->text('observations')->nullable();
            $table->text('conditions_reglement')->nullable();

            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero');
            $table->index('type_facture');
            $table->index('statut');
            $table->index('date_facture');
            $table->index('demande_pec_id');
            $table->index('partenaire_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
