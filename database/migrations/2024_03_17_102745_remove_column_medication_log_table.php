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
        Schema::table('medication_logs', function (Blueprint $table) {
            // 컬럼 삭제
            $table->dropColumn('drug_name');
            $table->dropColumn('drug_information');
            $table->foreignId('drug_id')->constrained('drugs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medication_logs', function (Blueprint $table) {
            // 컬럼 생성
            $table->string('drug_name')->index();
            $table->text('drug_information');
        });
    }
};
