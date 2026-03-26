<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seients_sessio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sessio_id')->constrained('sessions_cine')->onDelete('cascade');
            $table->string('fila');
            $table->integer('numero');
            $table->enum('estat', ['lliure', 'reservat', 'venut'])->default('lliure');
            $table->timestamp('reservat_at')->nullable();
            $table->timestamps();

            $table->unique(['sessio_id', 'fila', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seients_sessio');
    }
};
