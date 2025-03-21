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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense']);
            $table->foreignUuid('transaction_category_id')->constrained('transaction_categories');
            $table->foreignUuid('user_id')->constrained('users');
            $table->text('description')->nullable();
            $table->string('receipt_image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
