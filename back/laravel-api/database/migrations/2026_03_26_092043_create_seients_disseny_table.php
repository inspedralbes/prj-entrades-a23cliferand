<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seients_disseny', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id')->constrained('sales')->onDelete('cascade');
            $table->integer('fila');
            $table->integer('columna');
            $table->string('etiqueta_fila', 5);
            $table->integer('num_seient')->nullable();
            $table->boolean('es_seient')->default(true);
            $table->timestamps();

            $table->unique(['sala_id', 'fila', 'columna']);
            $table->index(['sala_id', 'etiqueta_fila']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seients_disseny');
    }
};
