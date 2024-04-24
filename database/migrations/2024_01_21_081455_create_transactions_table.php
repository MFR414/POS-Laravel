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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 100);
            $table->datetime('transaction_date');
            $table->string('sales_code', 100)->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_address', 100)->nullable();
            $table->bigInteger('item_total')->nullable();
            $table->float('discount_percentage')->nullable();
            $table->bigInteger('discount_total')->nullable();
            $table->float('tax_percentage')->nullable();
            $table->bigInteger('tax_total')->nullable();
            $table->bigInteger('other_fees')->nullable();
            $table->bigInteger('subtotal');
            $table->bigInteger('final_total');
            $table->bigInteger('dp_po')->nullable();
            $table->bigInteger('cash')->nullable();
            $table->bigInteger('credit')->nullable();
            $table->bigInteger('debit_card')->nullable();
            $table->bigInteger('credit_card')->nullable();
            $table->bigInteger('return')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};