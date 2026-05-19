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
        Schema::table('demandes_pec', function (Blueprint $table) {
            $table->enum('type_structure', ['clinique', 'laboratoire', 'médecin', 'radiologie'])->nullable()->after('ville');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_pec', function (Blueprint $table) {
            $table->dropColumn('type_structure');
        });
    }
};
