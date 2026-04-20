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
        Schema::table('stocks', function (Blueprint $table) {
            //
            $table->dropForeign(['medicament_id']);
            $table->dropColumn('medicament_id');
            $table->foreignId('medicament_id')->constrained('medicaments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            //
            $table->dropForeign(['medicament_id']);
            $table->dropColumn('medicament_id');
            $table->foreignId('medicament_id')->constrained('medicaments');
        });
    }
};
