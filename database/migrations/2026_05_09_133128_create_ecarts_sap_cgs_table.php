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
        Schema::create('ecarts_sap_cgs', function (Blueprint $table) {
            $table->id();
            $table->enum('type_ecart', [
                'Agent SAP absent CGS',
                'Agent CGS absent SAP',
                'Statut incohérent',
                'Âge ≥ 63 ans',
                'Données divergentes',
                'Ayants droit divergents'
            ]);
            $table->string('matricule', 20);
            $table->text('donnee_sap')->nullable();
            $table->text('donnee_cgs')->nullable();
            $table->text('details')->nullable();
            $table->date('date_detection');
            $table->boolean('traite')->default(false);
            $table->timestamp('date_traitement')->nullable();
            $table->foreignId('traite_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index
            $table->index('type_ecart');
            $table->index('matricule');
            $table->index('traite');
            $table->index('date_detection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecarts_sap_cgs');
    }
};
