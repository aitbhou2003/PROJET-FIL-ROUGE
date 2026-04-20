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
        Schema::table('ventes', function (Blueprint $table) {
            //
            $table->boolean('ordonnance_requise')->default(false);
            $table->string('ordonnance')->nullable();

            $table->decimal('remise_globale', 10, 2)->default(0);
            $table->decimal('remise_pourcentage', 5, 2)->nullable();

            $table->decimal('total_ht', 10, 2);
            $table->decimal('total_ttc', 10, 2);

            $table->enum('statut', ['en_cours', 'terminee', 'annulee'])->default('en_cours');
            $table->timestamp('date_annulation')->nullable();
            $table->text('motif_annulation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            //
        });
    }
};
