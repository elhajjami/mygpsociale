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
        Schema::table('factures', function (Blueprint $table) {
            $table->decimal('part_adherent', 10, 2)->nullable()->after('montant_autres')->comment('Part à charge de l\'adhérent');
            $table->decimal('part_cnops', 10, 2)->nullable()->after('part_adherent')->comment('Part prise en charge par la CNOPS');
            $table->decimal('part_assurance', 10, 2)->nullable()->after('part_cnops')->comment('Part prise en charge par l\'assurance complémentaire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn(['part_adherent', 'part_cnops', 'part_assurance']);
        });
    }
};
