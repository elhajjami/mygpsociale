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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained('demandes_pec')->onDelete('cascade');
            $table->decimal('montant_brut', 10, 2)->default(0);
            $table->decimal('deduction', 10, 2)->default(0);
            $table->decimal('montant_net', 10, 2)->default(0);
            $table->date('date_reception_facture')->nullable();
            $table->date('date_transmission_paiement')->nullable();
            $table->date('date_paiement')->nullable();
            $table->string('reference_paiement', 100)->nullable();
            $table->enum('statut_paiement', ['Non reçu', 'Reçu', 'En contrôle', 'Transmis paiement', 'Payé', 'Rejeté', 'Clôturé'])->default('Non reçu');
            $table->text('observations')->nullable();
            $table->timestamps();

            // Index
            $table->index('demande_id');
            $table->index('statut_paiement');
            $table->index('date_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
