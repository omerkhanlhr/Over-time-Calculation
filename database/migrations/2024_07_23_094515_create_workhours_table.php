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
        Schema::create('workhours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('client_id');
            $table->date('work_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('daily_workhours');
            $table->time('weekly_workhours');
            $table->integer('daily_overtime')->default(0);
            $table->time('weekly_overtime')->default('00:00');
            $table->boolean('overtime')->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workhours');
    }
};
