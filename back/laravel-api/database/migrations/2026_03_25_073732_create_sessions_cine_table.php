<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sessions_cine', function (Blueprint $table) {
            $table->id();
            $table->string('pellicula_id');
            $table->foreignId('sala_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('tarifa_id')->constrained('tarifes')->onDelete('cascade');
            $table->timestamp('data_hora');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions_cine');
    }
};
