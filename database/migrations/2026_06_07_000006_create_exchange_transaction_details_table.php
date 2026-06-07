<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_id');
            $table->foreignId('bottle_type_id')->constrained('bottle_types');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('points_earned');

            $table->foreign('transaction_id')->references('id')->on('exchange_transactions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_transaction_details');
    }
};
