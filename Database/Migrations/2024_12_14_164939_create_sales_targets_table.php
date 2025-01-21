<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('sales_targets')) {
            Schema::create('sales_targets', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('category_id');
                $table->unsignedInteger('business_id');
                $table->decimal('minimum_sales', 15, 2);
                $table->decimal('maximum_sales', 15, 2);
                $table->enum('commission_type', ['fixed', 'percentage']);
                $table->decimal('commission_value', 15, 2);
                $table->date('start_date');
                $table->date('end_date');
                //$table->enum('sales_goal', ['daily', 'weekly', 'monthly']);
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sales_targets');
    }
};
