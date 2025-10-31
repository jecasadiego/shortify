<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkClick extends Model
{
    protected $fillable = [
        'short_link_id','clicked_at','ip_hash','user_agent','referrer','country'
    ];

    protected $casts = ['clicked_at' => 'datetime'];

    public function link(){ return $this->belongsTo(ShortLink::class,'short_link_id'); }
}
