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
        Schema::create('no_connects', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->string('telephone')->unique()->nullable();
            $table->string('numero_identite')->unique()->nullable();
            $table->string('numero_registre')->unique()->nullable();
            $table->string('age');
            $table->enum('nationnalite', ['senegalais', 'etranger resident', 'etranger non resident']);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('no_connect');
    }
};
