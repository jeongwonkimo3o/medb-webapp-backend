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
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('entp_name', 50)->nullable(); // 제조사명
            $table->text('item_name')->nullable(); // 제품명
            $table->string('item_seq', 50)->nullable(); // 품목기준코드
            $table->text('efcy_qesitm')->nullable();    // 효능효과
            $table->text('use_method_qesitm')->nullable(); // 용법용량
            $table->text('atpn_warn_qesitm')->nullable(); // 주의사항
            $table->text('intrc_qesitm')->nullable(); // 병용금기
            $table->text('se_qesitm')->nullable(); // 부작용
            $table->text('deposit_method_qesitm')->nullable(); // 보관방법
            $table->string('item_image', 100)->nullable(); // 이미지
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drugs');
    }
};
