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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('feedback_id');
            $table->unsignedInteger('review_id'); 
            $table->enum('feedback', ['like', 'dislike']);
            $table->unsignedInteger('user_id'); 

            // foreign keys
            $table->foreign('review_id')->references('review_id')->on('reviews')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('restrict');

            // unique
            $table->unique(['review_id', 'user_id']);
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
