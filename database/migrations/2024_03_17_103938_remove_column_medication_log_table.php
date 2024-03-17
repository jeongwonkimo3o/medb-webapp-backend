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
        // start_date 컬럼 제거
        Schema::table('medication_logs', function (Blueprint $table) {
            $table->dropColumn('start_date');
            // last_date를 end_date로 변경
            $table->renameColumn('last_date', 'end_date');
            // 타임스탬프 컬럼 추가
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
