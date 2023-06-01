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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_id')->nullable();
            $table->foreign('employe_id')->references('id')->on('employes')->onDelete('CASCADE');
            $table->string('domaine_formation');
            $table->string('diplomes_obtenues');
            $table->string('intitule_formation');
            $table->integer('duree_formation');
            $table->integer('montant');
            $table->string('lieu_formation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
