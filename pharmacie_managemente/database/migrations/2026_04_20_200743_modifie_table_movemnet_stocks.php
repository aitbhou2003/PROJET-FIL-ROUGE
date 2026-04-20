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
        Schema::table('movement_stocks', function (Blueprint $table) {
            //
            $table->dropColumn('type');
            $table->enum('type', ['entree', 'sortie', 'ajustement', 'suppression', 'annulation']);
            $table->foreignId('vente_id')->nullable()->constrained('ventes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movement_stocks', function (Blueprint $table) {
            //
        });
    }
};
