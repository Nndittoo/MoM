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
        Schema::create('moms', function (Blueprint $table) {
            $table->id('version_id');
            $table->string('title');
            $table->date('meeting_date');
            
            $table->string('location'); 
            $table->time('start_time'); 
            $table->time('end_time'); 
            
        
            $table->string('pimpinan_rapat'); 
            $table->string('notulen');
            
            $table->foreignId('creator_id')->constrained('users'); 
            
            $table->longText('pembahasan');
            $table->foreignId('status_id')->constrained('mom_status', 'status_id');
            
            $table->json('nama_peserta'); // Peserta Internal (array nama)
            $table->json('nama_mitra'); // Peserta Mitra (array of objects)
            
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moms');
    }
};