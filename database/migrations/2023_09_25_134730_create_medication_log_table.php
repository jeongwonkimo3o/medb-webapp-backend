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
        Schema::create('medication_logs', function (Blueprint $table) {
            // 2023.12.16 Migration Refactoring(마이그레이션 리팩토링)
            // 테이블 이름 수정 - medication_log -> medication_logs
            // 기본 키 이름 id로 변경
            // 외래키 정의 방식 변경
            $table->id();
            $table->string('drug_name')->index();
            $table->text('drug_information');
            $table->timestamp('start_date');  
            $table->timestamp('last_date')->nullable();

            // foreign key
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_log');
    }
};
