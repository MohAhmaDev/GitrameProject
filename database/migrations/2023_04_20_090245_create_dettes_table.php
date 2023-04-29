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
            $table->unsignedBigInteger('creditor_id');
            $table->string('creditor_type');
            $table->unsignedBigInteger('debtor_id');
            $table->string('debtor_type');
            $table->string('intitule_projet');
            $table->string('num_fact');
            $table->string('num_situation');
            $table->date('date_dettes');
            $table->integer('montant');
            $table->string('observations');
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
