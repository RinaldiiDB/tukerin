<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redemption_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->unsignedInteger('points_used');
            $table->unsignedBigInteger('amount');
            $table->string('method', 20); // 'cash', 'ewallet'
            $table->string('bank_name', 100);
            $table->string('recipient_account', 100);
            $table->string('status', 20)->default('pending'); // 'pending', 'approved', 'rejected'
            $table->text('rejection_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redemption_requests');
    }
};
