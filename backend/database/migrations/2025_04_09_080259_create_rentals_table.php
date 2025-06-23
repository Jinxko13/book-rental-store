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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id(); // Tạo cột id tự tăng
            $table->foreignId('user_id')->constrained()->references('id')->on('users')->onDelete('cascade');
            $table->date('rental_date');
            $table->date('due_date');
            $table->enum('status', ['renting', 'returned', 'overdue', 'cancel'])->default('renting');
            $table->decimal('deposit', 10, 2);
            $table->decimal('rental_fee', 10, 2);
            $table->decimal('paid', 10, 2);
            $table->foreignId('discount_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
