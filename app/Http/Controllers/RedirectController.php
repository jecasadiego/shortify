<?php

namespace App\Http\Controllers;

use App\Models\LinkClick;
use App\Models\ShortLink;
use App\Support\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RedirectController extends Controller
{
    public function __invoke(Request $r, string $slug)
    {
        try {

            $link = ShortLink::where('slug', $slug)->firstOrFail();

            if (!$link->is_active || $link->isExpired()) {
                return ApiResponder::error('Link is not active', 404);
            }

            if ($link->password_hash) {
                $pwd = $r->query('pwd');
                if (!$pwd || !Hash::check($pwd, $link->password_hash)) {
                    return ApiResponder::error('Wrong password', 403);
                }
            }

            try {
                LinkClick::create([
                    'short_link_id' => $link->id,
                    'clicked_at'    => now(),
                    'ip_hash'       => hash('sha256', $r->ip() . config('app.key')),
                    'user_agent'    => substr((string)$r->userAgent(), 0, 255),
                    'referrer'      => substr((string)$r->headers->get('referer'), 0, 255) ?: null,
                    'country'       => null,
                ]);
                $link->increment('clicks_count');
            } catch (\Throwable $e) { /* no romper redirección */
            }

            return redirect()->away($link->destination_url);
        } catch (\Throwable $e) {
            return ApiResponder::error('An error occurred', 500);
        }
    }
}
