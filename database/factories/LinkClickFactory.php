<?php

namespace Database\Factories;

use App\Models\LinkClick;
use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkClickFactory extends Factory
{
    protected $model = LinkClick::class;

    public function definition(): array
    {
        return [
            'short_link_id' => ShortLink::factory(),
            'clicked_at'    => now(),
            'ip_hash'       => hash('sha256', $this->faker->ipv4() . config('app.key')),
            'user_agent'    => 'Mozilla/5.0',
            'referrer'      => 'https://example.com',
            'country'       => null,
        ];
    }
}
