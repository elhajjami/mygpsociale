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
        Schema::create('demandes_pec', function (Blueprint $table) {
            $table->id();
            $table->string('numero_demande', 50)->unique()->nullable();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->enum('beneficiaire_type', ['agent', 'conjoint', 'enfant']);
            $table->foreignId('ayant_droit_id')->nullable()->constrained('ayants_droit')->onDelete('set null');
            $table->enum('type_demande', ['consultation', 'radiologie', 'laboratoire', 'hospitalisation']);
            $table->string('nature_examens');
            $table->string('ville', 100);
            $table->foreignId('partenaire_id')->constrained()->onDelete('restrict');
            $table->string('specialite', 100)->nullable();
            $table->decimal('montant_devis', 10, 2)->default(0);
            $table->date('date_devis')->nullable();
            $table->string('fichier_devis', 255)->nullable();
            $table->boolean('urgence')->default(false);
            $table->text('observations')->nullable();

            // Statuts et workflow
            $table->enum('statut', [
                'En attente de validation',
                'Validée',
                'Rejetée',
                'Remise à l agent',
                'Expirée',
                'Reçue',
                'En cours de traitement',
                'Transmise au paiement',
                'Payée',
                'Clôturée',
                'Annulée'
            ])->default('En attente de validation');
            $table->text('motif_rejet')->nullable();
            $table->date('date_validation')->nullable();
            $table->date('date_expiration')->nullable();

            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_demande');
            $table->index('agent_id');
            $table->index('statut');
            $table->index('date_validation');
            $table->index('date_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_pec');
    }
};
