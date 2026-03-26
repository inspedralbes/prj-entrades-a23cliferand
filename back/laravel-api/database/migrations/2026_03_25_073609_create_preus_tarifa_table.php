<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preus_tarifa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarifa_id')->constrained('tarifes')->onDelete('cascade');
            $table->foreignId('tipus_client_id')->constrained('tipus_client')->onDelete('cascade');
            $table->decimal('preu', 8, 2);
            $table->timestamps();

            $table->unique(['tarifa_id', 'tipus_client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preus_tarifa');
    }
};
