<?php

namespace App\Http\Middleware;

use App\Events\PostViewersCount;
use App\Post;
use Closure;
use Illuminate\Support\Facades\Redis;

class Custom
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $name = $request->get('channel_name');

        broadcast(new PostViewersCount(Post::find((int)explode('.', $name)[1])));

        return $next($request);
    }
}
