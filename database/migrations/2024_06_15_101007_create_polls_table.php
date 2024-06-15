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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('facebook_link');
            $table->string('instagram_link');
            $table->string('status_in_media');
            $table->integer('from')->nullable();
            $table->integer('to')->nullable();
            $table->string('goal')->nullable();
            $table->string('note')->nullable();
            $table->enum('status', ['new', 'old'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
