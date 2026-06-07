<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('barcode')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('points_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottle_types');
    }
};
