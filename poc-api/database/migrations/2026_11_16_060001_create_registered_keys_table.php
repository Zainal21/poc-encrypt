<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registered_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key_id', 16)->unique();
            $table->string('name');
            $table->string('algorithm');
            $table->string('public_key_path')->nullable();
            $table->string('private_key_path')->nullable();
            $table->string('fingerprint')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registered_keys');
    }
};
