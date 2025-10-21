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
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('customer_name')->default('Test Name');
    $table->string('customer_phone')->nullable();
    $table->string('customer_address')->default('Sample Address');
    $table->decimal('subtotal', 10, 2);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    $table->string('coupon_code')->nullable();
    $table->tinyInteger('status')->default(1)->comment('1=pending, 2=paid, 3=shipped, 4=completed, 5=cancelled');
    $table->softDeletes();
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
