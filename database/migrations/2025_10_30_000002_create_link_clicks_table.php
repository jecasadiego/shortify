<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('link_clicks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('short_link_id')
                ->constrained('short_links')
                ->cascadeOnDelete();

            // Registro del clic
            $table->dateTime('clicked_at')->index();

            // Datos de analítica (privacidad: IP hasheada)
            $table->char('ip_hash', 64);
            $table->string('user_agent', 255);
            $table->string('referrer', 255)->nullable();
            $table->char('country', 2)->nullable(); // opcional

            $table->timestamps();

            // Índices útiles para agregaciones
            $table->index(['short_link_id', 'clicked_at']);
            $table->index('referrer');
            $table->index('user_agent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_clicks');
    }
};
