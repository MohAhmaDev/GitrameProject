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
            $table->unsignedBigInteger('filiale_id');
            $table->string('activite');
            $table->string('type_activite');
            $table->date('date_activite');
            $table->string('compte_scf');
            $table->integer('privision');
            $table->integer('realisation');
            $table->timestamps();
            $table->foreign('filiale_id')->references('id')->on('filiales')->onDelete('CASCADE');
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
