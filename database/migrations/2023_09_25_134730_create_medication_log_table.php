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
        Schema::create('medication_log', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedInteger('user_id'); 
            $table->string('drug_name')->index();
            $table->text('drug_information');
            $table->timestamp('start_date');  
            $table->timestamp('last_date')->nullable();

            // foreign key
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
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
