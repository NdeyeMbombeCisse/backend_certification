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
        Schema::create('trajets', function (Blueprint $table) {
            $table->id();
            $table->date('date_depart');
            $table->date('date_arrivee');
            $table->string('lieu_depart');
            $table->string('lieu_arrive');
            $table->string('image');
            $table->boolean('statut'); 
            $table->string('heure_embarquement');
            $table->string('heure_depart');
            $table->unsignedBigInteger('bateau_id'); // Colonne pour la clé étrangère
            $table->foreign('bateau_id')->references('id')->on('bateaus'); // Définition de la clé étrangère
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajets');
    }
};
