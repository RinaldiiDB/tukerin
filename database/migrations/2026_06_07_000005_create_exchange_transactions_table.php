<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('employee_id');
            $table->unsignedInteger('total_points');
            $table->timestamp('transacted_at');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('employee_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_transactions');
    }
};
