<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('slug', 15)->unique();
            $table->text('destination_url');

            // Protegido opcionalmente con contraseña
            $table->string('password_hash')->nullable();

            // Expiración opcional
            $table->dateTime('expires_at')->nullable()->index();

            // Estado y contador rápido
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('clicks_count')->default(0);

            $table->timestamps();

            // Índices adicionales útiles
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_links');
    }
};
