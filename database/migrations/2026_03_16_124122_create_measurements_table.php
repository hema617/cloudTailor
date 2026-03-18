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
        Schema::create('measurements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->decimal('chest',5,2)->nullable();
    $table->decimal('waist',5,2)->nullable();
    $table->decimal('hip',5,2)->nullable();
    $table->decimal('shoulder',5,2)->nullable();
    $table->decimal('sleeve_length',5,2)->nullable();
    $table->decimal('dress_length',5,2)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
