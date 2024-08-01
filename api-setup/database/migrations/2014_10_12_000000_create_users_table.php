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
            $table->string('avatar');
            $table->string('username')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('verification_code')->unique()->nullable();
            $table->boolean('email_verified')->default(false);
            $table->enum('role', ['Member', 'Business', 'Admin']);
            $table->string('phone_number')->nullable();
            $table->string('domicile')->nullable();
            $table->string('gender')->nullable();
            $table->string('pronouns')->nullable();
            $table->longText('bio')->nullable();
            $table->boolean('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
