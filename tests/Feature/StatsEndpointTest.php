<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\ShortLink;
use App\Models\LinkClick;

class StatsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_summary_returns_expected_shape(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $link = ShortLink::create([
            'user_id'        => $user->id,
            'slug'           => 'stat',
            'destination_url'=> 'https://example.com',
            'is_active'      => true,
            'clicks_count'   => 5, // simulamos total rápido
        ]);

        // Insertar algunos clicks (2 hace 2 días, 3 hoy)
        LinkClick::create([
            'short_link_id' => $link->id,
            'clicked_at'    => now()->subDays(2),
            'ip_hash'       => hash('sha256', '1.1.1.1'.config('app.key')),
            'user_agent'    => 'UA1',
            'referrer'      => 'https://google.com',
        ]);
        LinkClick::create([
            'short_link_id' => $link->id,
            'clicked_at'    => now()->subDays(2),
            'ip_hash'       => hash('sha256', '2.2.2.2'.config('app.key')),
            'user_agent'    => 'UA1',
            'referrer'      => 'https://google.com',
        ]);
        LinkClick::create([
            'short_link_id' => $link->id,
            'clicked_at'    => now(),
            'ip_hash'       => hash('sha256', '3.3.3.3'.config('app.key')),
            'user_agent'    => 'UA2',
            'referrer'      => null,
        ]);
        LinkClick::create([
            'short_link_id' => $link->id,
            'clicked_at'    => now(),
            'ip_hash'       => hash('sha256', '4.4.4.4'.config('app.key')),
            'user_agent'    => 'UA2',
            'referrer'      => null,
        ]);
        LinkClick::create([
            'short_link_id' => $link->id,
            'clicked_at'    => now(),
            'ip_hash'       => hash('sha256', '5.5.5.5'.config('app.key')),
            'user_agent'    => 'UA2',
            'referrer'      => null,
        ]);

        $res = $this->getJson("/api/v1/links/{$link->id}/stats/summary")
            ->assertOk()
            ->assertJsonStructure([
                'total_clicks',
                'last_click_at',
                'days_30_clicks' => [['date','count']],
                'top_referrers'  => [['referrer','count']],
                'top_agents'     => [['agent','count']],
            ]);

        $this->assertSame(5, (int) $res->json('total_clicks'));
    }
}
