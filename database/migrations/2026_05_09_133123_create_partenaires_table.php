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
        Schema::create('partenaires', function (Blueprint $table) {
            $table->id();
            $table->string('numero_convention', 50)->unique();
            $table->string('nom', 200);
            $table->enum('type_structure', ['clinique', 'laboratoire', 'médecin', 'radiologie']);
            $table->string('ville', 100);
            $table->string('specialite', 100)->nullable();
            $table->date('date_effet')->nullable();
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['active', 'expirée', 'suspendue', 'résiliée'])->default('active');
            $table->text('coordonnees')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('numero_convention');
            $table->index('type_structure');
            $table->index('ville');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partenaires');
    }
};
