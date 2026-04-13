<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('seients_sessio', function (Blueprint $table) {
            $table->foreignId('usuari_id')->nullable()->constrained('usuaris')->onDelete('set null')->after('reservat_at');
            $table->string('guest_id')->nullable()->after('usuari_id');
        });
    }

    public function down(): void
    {
        Schema::table('seients_sessio', function (Blueprint $table) {
            $table->dropConstrainedForeignId('usuari_id');
            $table->dropColumn('guest_id');
        });
    }
};
