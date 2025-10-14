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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['mom_pending', 'task_urgent', 'user_new', 'task_overdue']);
            $table->string('title');
            $table->text('message');
            $table->foreignId('related_id')->nullable(); // mom_id, action_id, atau user_id
            $table->boolean('is_read')->default(false);
            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
