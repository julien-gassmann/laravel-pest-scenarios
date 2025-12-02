<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dummy_children', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->foreignId('dummy_id')->constrained('dummies');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dummy_children');
    }
};
