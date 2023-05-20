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
        Schema::table('filiales', function (Blueprint $table) {
            $table->string('groupe')->default('Gitrama');
            $table->string('secteur')->default('traveaux public');
            $table->string('nationalite')->default('algerienne');
            $table->string('status_juridique')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filiales', function (Blueprint $table) {
            //
        });
    }
};
