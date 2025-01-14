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
        Schema::create('journalists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->OnDelete('cascade');
            $table->string('folder')->nullable();
            $table->string('file')->nullable();
            $table->string('sheet')->nullable();
            $table->string('ccaa')->nullable();
            $table->string('geographical_scope')->nullable();
            $table->string('category')->nullable();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->nullable();
            $table->string('email')->nullable();
            $table->boolean('status')->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journalists');
    }
};
