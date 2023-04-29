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
        Schema::create('stagiares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero_securite_social')->unique();
            $table->unsignedBigInteger('filiale_id');
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('domaine_formation');
            $table->string('diplomes_obtenues');
            $table->string('intitule_formation');
            $table->integer('duree_formation');
            $table->integer('montant');
            $table->string('lieu_formation');
            $table->timestamps();
            $table->foreign('filiale_id')->references('id')->on('filiales')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stagiares');
    }
};
