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
        Schema::create('coupons', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->enum('discount_type',['percentage','fixed']);
    $table->decimal('discount_value',10,2);
    $table->integer('max_usage')->nullable();
    $table->integer('used_count')->default(0);
    $table->timestamp('expires_at')->nullable();
    $table->boolean('status')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
