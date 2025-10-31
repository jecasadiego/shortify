<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use App\Models\User;
use App\Models\ShortLink;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;

class RedirectEndpointTest extends TestCase
{
    use RefreshDatabase;
    const DESTINATION_URL = 'https://laravel.com';
    const EXAMPLE_URL = 'https://example.com';
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_redirects_and_counts_click(): void
    {
        $user = User::factory()->create();
        $link = ShortLink::create([
            'user_id'        => $user->id,
            'slug'           => 'go',
            'destination_url'=> $this::DESTINATION_URL,
            'is_active'      => true,
            'clicks_count'   => 0,
        ]);

        $this->get('/r/go')->assertRedirect( $this::DESTINATION_URL);

        $link->refresh();
        $this->assertSame(1, (int) $link->clicks_count);
    }

    public function test_expired_or_inactive_returns_410(): void
    {
        $user = User::factory()->create();

        // Expirado
        ShortLink::create([
            'user_id'        => $user->id,
            'slug'           => 'old',
            'destination_url'=>  $this::EXAMPLE_URL,
            'is_active'      => true,
            'expires_at'     => now()->subDay(),
        ]);
        $this->get('/r/old')->assertStatus(410);

        // Inactivo
        ShortLink::create([
            'user_id'        => $user->id,
            'slug'           => 'off',
            'destination_url'=> $this::EXAMPLE_URL,
            'is_active'      => false,
        ]);
        $this->get('/r/off')->assertStatus(410);
    }

    public function test_password_protected_requires_pwd(): void
    {
        $user = User::factory()->create();
        ShortLink::create([
            'user_id'        => $user->id,
            'slug'           => 'lock',
            'destination_url'=> $this::EXAMPLE_URL,
            'is_active'      => true,
            'password_hash'  => Hash::make('topsecret'),
        ]);

        $this->get('/r/lock')->assertStatus(403);
        $this->get('/r/lock?pwd=topsecret')->assertRedirect($this::EXAMPLE_URL);
    }
}
