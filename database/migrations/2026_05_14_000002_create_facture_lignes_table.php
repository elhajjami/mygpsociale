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
        Schema::create('facture_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained()->onDelete('cascade');

            // Type de ligne (pour organiser dans la facture)
            $table->enum('type_ligne', ['acte', 'prestation_clinique', 'honoraire', 'autre'])->default('acte');

            // Informations acte médical (type medical)
            $table->string('matricule')->nullable()->comment('Matricule agent/patient');
            $table->string('nom_patient')->nullable()->comment('Nom et prénom du patient');
            $table->string('beneficiaire')->nullable()->comment('Agent, conjoint, enfant');
            $table->string('nature_acte')->nullable()->comment('Nature de l\'examen ou acte');
            $table->string('cotation')->nullable()->comment('Lettre clé de cotation');

            // Informations prestation (type clinique/honoraire)
            $table->string('designation')->nullable()->comment('Désignation de la prestation');
            $table->string('categorie')->nullable()->comment('Catégorie: séjour, bloc, pharmacie, chirurgien, anesthesiste, labo, etc.');

            // Quantité et prix
            $table->decimal('quantite', 10, 2)->default(1);
            $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->decimal('montant', 10, 2)->default(0);

            // Ordre d'affichage
            $table->integer('ordre')->default(0);

            $table->timestamps();

            // Index
            $table->index('facture_id');
            $table->index('type_ligne');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_lignes');
    }
};
