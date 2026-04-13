<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserva_seient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reserva')->onDelete('cascade');
            $table->foreignId('seient_sessio_id')->constrained('seients_sessio')->onDelete('cascade');
            $table->foreignId('tipus_client_id')->constrained('tipus_client')->onDelete('cascade');
            $table->decimal('preu_aplicat', 8, 2);
            $table->timestamps();

            $table->unique(['reserva_id', 'seient_sessio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva_seient');
    }
};
