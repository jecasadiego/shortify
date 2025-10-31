<?php

namespace App\Http\Controllers;

use App\Models\LinkClick;
use App\Models\ShortLink;
use App\Support\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function summary(Request $r, $id)
    {
        try{

            $link = ShortLink::where('user_id', $r->user()->id)->findOrFail($id);

            $cacheKey = "stats.summary.{$link->id}";
            $data = Cache::remember($cacheKey, 60, function () use ($link) {
                $total = $link->clicks_count;
                $last  = LinkClick::where('short_link_id', $link->id)->latest('clicked_at')->value('clicked_at');

                $days30 = LinkClick::where('short_link_id', $link->id)
                    ->where('clicked_at', '>=', now()->subDays(30))
                    ->selectRaw('DATE(clicked_at) as date, COUNT(*) as count')
                    ->groupBy('date')->orderBy('date')->get();

                $referrers = LinkClick::where('short_link_id', $link->id)
                    ->selectRaw('COALESCE(referrer,"(direct)") as referrer, COUNT(*) as count')
                    ->groupBy('referrer')->orderByDesc('count')->limit(5)->get();

                $agents = LinkClick::where('short_link_id', $link->id)
                    ->selectRaw('user_agent as agent, COUNT(*) as count')
                    ->groupBy('agent')->orderByDesc('count')->limit(5)->get();

                return [
                    'total_clicks'  => $total,
                    'last_click_at' => $last,
                    'days_30_clicks' => $days30,
                    'top_referrers' => $referrers,
                    'top_agents'    => $agents,
                ];
            });

            return ApiResponder::success($data, 'OK', 200);
        }catch (\Throwable $e) {
            return ApiResponder::error($e->getMessage(), 500);
        }
    }
}
