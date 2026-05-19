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
        Schema::table('partenaires', function (Blueprint $table) {
            // Champs déjà présents vérifiés - on ajoute seulement ceux qui manquent
            if (!Schema::hasColumn('partenaires', 'adresse')) {
                $table->string('adresse')->nullable();
            }
            if (!Schema::hasColumn('partenaires', 'rib')) {
                $table->string('rib', 24)->nullable()->comment('RIB bancaire');
            }
            if (!Schema::hasColumn('partenaires', 'banque')) {
                $table->string('banque')->nullable();
            }
            if (!Schema::hasColumn('partenaires', 'agence')) {
                $table->string('agence')->nullable();
            }
            if (!Schema::hasColumn('partenaires', 'ice')) {
                $table->string('ice', 20)->nullable()->comment('Identifiant Commun de l\'Entreprise');
            }
            if (!Schema::hasColumn('partenaires', 'patente')) {
                $table->string('patente')->nullable();
            }
            if (!Schema::hasColumn('partenaires', 'if')) {
                $table->string('if')->nullable()->comment('Identifiant Fiscal');
            }
            if (!Schema::hasColumn('partenaires', 'cnss')) {
                $table->string('cnss')->nullable();
            }
            if (!Schema::hasColumn('partenaires', 'fax')) {
                $table->string('fax')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partenaires', function (Blueprint $table) {
            $table->dropColumn([
                'adresse', 'rib', 'banque', 'agence',
                'ice', 'patente', 'if', 'cnss', 'fax'
            ]);
        });
    }
};
