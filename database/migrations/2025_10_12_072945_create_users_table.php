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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->string('username')->unique();
            $table->text('password')->nullable();
            $table->string('roles')->nullable();
            $table->string('profilepic')->nullable();
            $table->integer('isactivated')->default(0);
            $table->integer('isblocked')->default(0);
            $table->integer('mailtoken')->default(0);
            $table->text('qrcodeurl')->nullable();
            $table->text('secretkey')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
