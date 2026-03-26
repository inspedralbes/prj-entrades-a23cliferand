<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuari_id')->constrained('usuaris')->onDelete('cascade');
            $table->foreignId('sessio_id')->constrained('sessions_cine')->onDelete('cascade');
            $table->decimal('preu_total', 10, 2);
            $table->enum('estat', ['pendent', 'confirmada', 'caducada'])->default('pendent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva');
    }
};
