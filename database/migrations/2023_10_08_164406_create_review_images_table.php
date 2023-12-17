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
        Schema::create('review_images', function (Blueprint $table) {
            // 2023.12.16 Migration Refactoring(마이그레이션 리팩토링)
            // 기본 키 이름 id로 변경
            // 외래키 정의 방식 변경
            $table->id();
            $table->string('image_url', 512);
            $table->string('image_key');
            $table->timestamps();

            // foreign key
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_images');
    }
};
