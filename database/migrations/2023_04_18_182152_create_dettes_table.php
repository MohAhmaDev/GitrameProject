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
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_entreprise');
            $table->unsignedBigInteger('id_ent_debitrice');
            $table->string('nom_ent_debitrice');
            $table->string('intitule_projet');
            $table->string('num_fact');
            $table->string('num_situation');
            $table->date('date_dettes');
            $table->unsignedBigInteger('montant');
            $table->unsignedBigInteger('filiale_id');
            $table->string('observations');
            $table->foreign('id_entreprise')->references('id')->on('entreprises');
            $table->foreign('id_ent_debitrice')->references('id')->on('entreprises');
            $table->foreign('filiale_id')->references('id')->on('filiales');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dettes');
    }
};
