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
        Schema::create('pharmacy_guards', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('pharmacy_id')
                ->constrained('pharmacies')
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['date', 'pharmacy_id']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_guards');
    }
};
