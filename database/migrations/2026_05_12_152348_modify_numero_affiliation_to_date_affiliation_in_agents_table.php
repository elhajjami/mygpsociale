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
        Schema::table('agents', function (Blueprint $table) {
            // Renommer la colonne et changer le type
            $table->renameColumn('numero_affiliation', 'date_affiliation');
        });

        // Maintenant changer le type en date
        Schema::table('agents', function (Blueprint $table) {
            $table->date('date_affiliation')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Revenir à string
            $table->string('numero_affiliation', 50)->nullable()->change();
        });

        Schema::table('agents', function (Blueprint $table) {
            // Renommer
            $table->renameColumn('date_affiliation', 'numero_affiliation');
        });
    }
};
