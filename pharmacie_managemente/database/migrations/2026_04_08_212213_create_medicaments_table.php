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
        Schema::create('medicaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained();
            $table->string('nom');
            $table->string('code_barre')->unique();
            $table->text('description')->nullable();
            $table->string('fabricant');
            $table->string('forme_dosage');
            $table->boolean('ordonnance_requise')->default(false);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicaments');
    }
};
