<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label', 30)->nullable();
            $table->string('recipient', 100);
            $table->string('line1', 200);
            $table->string('line2', 200)->nullable();
            $table->string('city', 50);
            $table->string('district', 50);
            $table->string('postal_code', 5);
            $table->string('phone', 20);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
