<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres_plafond', function (Blueprint $table) {
            $table->id();
            $table->year('annee')->unique();
            $table->decimal('plafond_execution', 10, 2)->default(12000); // Exécution
            $table->decimal('plafond_maitrise', 10, 2)->default(15000); // Maîtrise
            $table->decimal('plafond_cadre', 10, 2)->default(18000); // Cadre
            $table->decimal('plafond_hors_cadre', 10, 2)->default(18000); // Hors cadre
            $table->decimal('plafond_bo', 10, 2)->default(18000); // BO (si différent)
            $table->boolean('actif')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('annee');
        });

        // Insérer les données par défaut pour l'année courante
        Artisan::call('db:seed', ['--class' => 'ParametrePlafondSeeder']);
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres_plafond');
    }
};
