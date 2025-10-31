<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use App\Support\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShortLinkController extends Controller
{
    public function index(Request $r)
    {
        return ShortLink::where('user_id', $r->user()->id)
            ->latest()->paginate(10);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'destination_url' => 'required|url|max:2000',
            'slug'            => 'nullable|alpha_num|lowercase|min:4|max:15|unique:short_links,slug',
            'password'        => 'nullable|string|min:3|max:100',
            'expires_at'      => 'nullable|date|after:now',
        ]);

        $slug = $data['slug'] ?? Str::lower(Str::random(8));

        $link = ShortLink::create([
            'user_id'        => $r->user()->id,
            'slug'           => $slug,
            'destination_url' => $data['destination_url'],
            'password_hash'  => isset($data['password']) ? Hash::make($data['password']) : null,
            'expires_at'     => $data['expires_at'] ?? null,
            'is_active'      => true,
            'clicks_count'   => 0,
        ]);

        return ApiResponder::success($link, 'Created', 201);
    }

    public function show(Request $r, $id)
    {
        try{

            $link = ShortLink::findOrFail($id);
            $this->authorize('view', $link);
            return ApiResponder::success($link, 'OK', 200);
        }catch (\Throwable $e) {
            return ApiResponder::error($e->getMessage(), 500);
        }
    }

    public function update(Request $r, $id)
    {
        try{

            $link = ShortLink::findOrFail($id);
            $this->authorize('update', $link);

            $data = $r->validate([
                'destination_url' => 'sometimes|url|max:2000',
                'password'        => 'nullable|string|min:3|max:100',
                'expires_at'      => 'nullable|date|after:now',
                'is_active'       => 'sometimes|boolean',
            ]);

            if (array_key_exists('password', $data)) {
                $link->password_hash = $data['password'] ? Hash::make($data['password']) : null;
            }

            $link->fill([
                'destination_url' => $data['destination_url'] ?? $link->destination_url,
                'expires_at'      => $data['expires_at'] ?? $link->expires_at,
                'is_active'       => $data['is_active'] ?? $link->is_active,
            ])->save();

            return ApiResponder::success($link, 'Updated', 200);
        }catch (\Throwable $e) {
            return ApiResponder::error($e->getMessage(), 500);
        }
    }

    public function destroy(Request $r, $id)
    {
        $link = ShortLink::findOrFail($id);
        $this->authorize('delete', $link);

        $link->is_active = false;
        $link->save();

        return ApiResponder::success($link, 'Deactivated', 204);
    }
}
