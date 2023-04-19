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
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_securite_social')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('domaine_formation');
            $table->string('diplomes_obtenues');
            $table->string('intitule_formation');
            $table->unsignedBigInteger('duree_formation');
            $table->unsignedBigInteger('montant');
            $table->string('lieu_formation');
            $table->foreign('id_entreprise')->references('id')->on('entreprises')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};
