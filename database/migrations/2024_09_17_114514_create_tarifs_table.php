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
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('tarif');
            $table->enum('nationnalite', ['senegalais', 'etranger resident', 'etranger non resident']);   
            $table->unsignedBigInteger('categorie_id'); // Colonne pour la clé étrangère
            $table->foreign('categorie_id')->references('id')->on('categories'); // Définition de la clé étrangère     
            $table->string('libelle')   ; 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifs');
    }
};
