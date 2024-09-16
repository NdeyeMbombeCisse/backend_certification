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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->date('date_reservation');
            $table->boolean('statut'); 
            $table->unsignedBigInteger('trajet_id'); // Colonne pour la clé étrangère
            $table->foreign('trajet_id')->references('id')->on('trajets'); // Définition de la clé étrangère
            $table->unsignedBigInteger('user_id'); // Colonne pour la clé étrangère
            $table->foreign('user_id')->references('id')->on('users'); // Définition de la clé étrangère
            $table->unsignedBigInteger('place_id'); // Colonne pour la clé étrangère
            $table->foreign('place_id')->references('id')->on('places'); // Définition de la clé étrangère
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
