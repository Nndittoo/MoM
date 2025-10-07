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
        Schema::create('mom_attendees', function (Blueprint $table) {
            $table->foreignId('mom_id')->constrained('moms', 'version_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 

            $table->primary(['mom_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mom_attendees');
    }
};
