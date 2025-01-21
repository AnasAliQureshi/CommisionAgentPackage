<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('commissions')) { // Create only if not exists
            Schema::create('commissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id'); // Foreign key to users table
                $table->unsignedInteger('category_id'); // Foreign key to categories table
                $table->decimal('sales_amount', 15, 2); // Sales amount
                $table->enum('commission_type', ['fixed', 'percentage']); // Type of commission
                $table->decimal('commission_amount', 15, 2); // Computed commission
                $table->date('transaction_date'); // Date of the transaction
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('commissions'); // Drop table if exists
    }
};