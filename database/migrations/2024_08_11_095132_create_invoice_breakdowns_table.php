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
        Schema::create('invoice_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('labor_type_id')->constrained('labours')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('total_employees');
            $table->decimal('rate', 10, 2);
            $table->integer('hours_worked'); // This will store the total hours as an integer
            $table->decimal('total_amount', 15, 2);
            $table->decimal('subtotal', 15, 2);
            // $table->decimal('overtime_hours', 10, 2)->nullable();
            // $table->decimal('overtime_amount', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_breakdowns');
    }
};
