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
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->unsignedBigInteger('categorie_id'); // Colonne pour la clé étrangère
            $table->foreign('categorie_id')->references('id')->on('categories'); // Définition de la clé étrangère
            $table->unsignedBigInteger('tarif_id'); // Colonne pour la clé étrangère
            $table->foreign('tarif_id')->references('id')->on('tarifs'); // Définition de la clé étrangère
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
