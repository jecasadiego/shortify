<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\ShortLink;

class ApiSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_and_login_endpoints(): void
    {
        // Register
        $res = $this->postJson('/api/v1/register', [
            'name'     => 'Juan',
            'email'    => 'juan@example.com',
            'password' => 'secret123',
        ])->assertCreated();

        $this->assertIsString($res->json('token'));

        // Login
        $res2 = $this->postJson('/api/v1/login', [
            'email'    => 'juan@example.com',
            'password' => 'secret123',
        ])->assertOk();

        $this->assertIsString($res2->json('token'));
    }

    public function test_links_crud_flow(): void
    {
        // Autenticar
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        // CREATE
        $create = $this->postJson('/api/v1/links', [
            'destination_url' => 'https://laravel.com',
            'slug'            => 'laravel',
        ])->assertCreated();

        $id = $create->json('id');
        $this->assertNotEmpty($id);
        $this->assertStringContainsString('/r/laravel', $create->json('short_url'));

        // INDEX (solo míos)
        $this->getJson('/api/v1/links')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        // SHOW
        $this->getJson("/api/v1/links/{$id}")
            ->assertOk()
            ->assertJsonPath('slug', 'laravel');

        // UPDATE
        $this->patchJson("/api/v1/links/{$id}", [
            'destination_url' => 'https://example.com',
            'is_active'       => false,
        ])->assertOk()
          ->assertJsonPath('destination_url', 'https://example.com')
          ->assertJsonPath('is_active', false);

        // DELETE (desactivar)
        $this->deleteJson("/api/v1/links/{$id}")
            ->assertNoContent();

        $link = ShortLink::find($id);
        $this->assertFalse((bool) $link->is_active);
    }
}
