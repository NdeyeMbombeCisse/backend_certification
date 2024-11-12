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
            $table->boolean('statut'); 
            $table->unsignedBigInteger('trajet_id'); 
            $table->foreign('trajet_id')->references('id')->on('trajets'); 
            $table->unsignedBigInteger('user_id')->nullable(); // Colonne pour la clé étrangère
            $table->foreign('user_id')->references('id')->on('users'); // Définition de la clé étrangère
            $table->unsignedBigInteger('place_id'); // Colonne pour la clé étrangère
            $table->foreign('place_id')->references('id')->on('places'); // Définition de la clé étrangère
            $table->boolean('is_paid')->default(false);
            $table->unsignedBigInteger('no_connect_id')->nullable(); 
            $table->foreign('no_connect_id')->references('id')->on('no_connects') ->onDelete('cascade'); 
        
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
