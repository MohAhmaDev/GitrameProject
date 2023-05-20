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
        Schema::create('admission_user', function (Blueprint $table) {
            $table->primary(['admission_id', 'user_id']);
            $table->unsignedBigInteger('admission_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        
            $table->foreign('admission_id')->references('id')->on('admissions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_user');
    }
};
