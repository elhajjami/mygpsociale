<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrieres', function (Blueprint $table) {
            $table->id();
            $table->string('code_niveau', 10)->unique(); // Ex: E1, E2, M1, M2, C1, H1
            $table->string('libelle_niveau', 100); // Ex: Exécution 1ère échelle
            $table->enum('categorie', ['Exécution', 'Maîtrise', 'Cadre', 'Hors cadre']);
            $table->string('prefixe_niveau', 5); // Ex: E, M, C, H
            $table->integer('ordre')->default(0); // Pour le tri
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index('categorie');
            $table->index('prefixe_niveau');
        });

        // Insérer les données par défaut
        Artisan::call('db:seed', ['--class' => 'CarriereSeeder']);
    }

    public function down(): void
    {
        Schema::dropIfExists('carrieres');
    }
};
