<?php

namespace Database\Factories;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShortLinkFactory extends Factory
{
    protected $model = ShortLink::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'slug'           => Str::lower(Str::random(8)),
            'destination_url' => $this->faker->url(),
            'password_hash'  => null,
            'expires_at'     => null,
            'is_active'      => true,
            'clicks_count'   => 0,
        ];
    }

    public function expired(): self
    {
        return $this->state(fn() => ['expires_at' => now()->subDay()]);
    }

    public function withPassword(string $plain = 'secret'): self
    {
        return $this->state(fn() => ['password_hash' => bcrypt($plain)]);
    }
}
