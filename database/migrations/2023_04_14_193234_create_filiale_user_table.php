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
        Schema::create('filiale_user', function (Blueprint $table) {
            $table->primary(['filiale_id', 'user_id']);
            $table->unsignedBigInteger('filiale_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        
            $table->foreign('filiale_id')->references('id')->on('filiales')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filiale_user');
    }
};
