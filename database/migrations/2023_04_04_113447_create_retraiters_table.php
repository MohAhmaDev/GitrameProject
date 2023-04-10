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
        Schema::create('retraiters', function (Blueprint $table) {
            $table->id();
            $table->string('categorie_socio_profetionnelle');
            $table->string('position');
            $table->date('date_retraite');
            $table->mediumText('observation');
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retraiters');
    }
};
