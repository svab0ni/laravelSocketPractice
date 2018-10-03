<?php

namespace App\Events;

use App\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Redis;
use Pusher\Pusher;

class PostViewersCount implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $count;
    private $post;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        //dd( Redis::incr("INSIGHT:VIEWERS:POST:$post->id:COUNT"));
        $this->post = $id;
        $this->count = Redis::get("INSIGHT:VIEWERS:POST:$id:COUNT");
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('posts.' . $this->post . '.count');
    }
}
