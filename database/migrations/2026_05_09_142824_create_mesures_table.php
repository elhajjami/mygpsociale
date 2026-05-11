<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesures', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Ex: DE, DC, SU, RE
            $table->string('libelle', 100); // Ex: Départ définitif, Décès, Suspension, Retraite
            $table->enum('type', ['sortie', 'suspension', 'retraite', 'actif'])->default('actif');
            $table->string('statut_correspondant', 50)->nullable(); // Statut CGS correspondant
            $table->boolean('bloque_pec')->default(false); // Bloque la PEC si true
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index('type');
        });

        // Insérer les données par défaut
        Artisan::call('db:seed', ['--class' => 'MesureSeeder']);
    }

    public function down(): void
    {
        Schema::dropIfExists('mesures');
    }
};
