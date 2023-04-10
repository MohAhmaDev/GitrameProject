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
            $table->string('domaine');              
            $table->string('diplome_obtenue');      
            $table->string('duree_formation');      
            $table->string('lieu_formation');       
            $table->integer('montant');             
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
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
