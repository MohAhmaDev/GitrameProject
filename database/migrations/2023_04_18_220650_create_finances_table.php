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
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_entreprise');
            $table->string('activite');
            $table->string('type_activite');
            $table->date('date_act');
            $table->string('compte_scf');
            $table->integer('privision');
            $table->integer('realisation');
            $table->foreign('id_entreprise')->references('id')->on('entreprises')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
