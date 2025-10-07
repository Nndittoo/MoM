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
        Schema::create('mom_agendas', function (Blueprint $table) {
            $table->id('agenda_id');
            $table->foreignId('mom_id')->constrained('moms', 'version_id')->onDelete('cascade');
            $table->text('item'); 
            $table->unsignedSmallInteger('order');
            $table->timestamps();
            
            // Menambah unique constraint untuk item dan urutan per MoM
            $table->unique(['mom_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mom_agendas');
    }
};