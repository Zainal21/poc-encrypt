<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_data_symmetric', function (Blueprint $table) {
            $table->id();
            $table->text('full_name');
            $table->string('nik', 16)->index();
            $table->text('email');
            $table->text('phone');
            $table->date('birth_date');
            $table->text('address');
            $table->json('raw_payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_data_symmetric');
    }
};
