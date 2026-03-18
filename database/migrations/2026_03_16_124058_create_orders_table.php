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
    $table->string('order_number')->unique();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('tailor_id')->constrained()->cascadeOnDelete();
    $table->decimal('total_amount',10,2);
    $table->string('delivery_type');
    $table->decimal('delivery_charge',10,2)->default(0);
    $table->foreignId('address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
    $table->string('status')->default('pending');
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
