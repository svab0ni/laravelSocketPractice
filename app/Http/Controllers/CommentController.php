<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Events\NewComment;
use App\Events\PostViewersCount;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        return response()->json($post->comments()->with('user')->latest()->get());
    }

    public function store(Request $request, Post $post)
    {
        $comment = $post->comments()->create([
           'body' => $request->body,
           'user_id' => Auth::id()
        ]);

        $comment = Comment::where('id', $comment->id)->with('user')->first();

        event(new NewComment($comment));

        return response()->json($comment);
    }

    public function closed(Request $request)
    {
        $events = $request->get('events');

        foreach ($events as $event)
        {
            try {
                $id = explode('_', $event['channel'])[1];

                if($event['name'] === 'channel_occupied')
                {
                    Redis::incr("INSIGHT:VIEWERS:POST:$id:COUNT");

                } else if($event['name'] === 'channel_vacated') {
                    Redis::decr("INSIGHT:VIEWERS:POST:$id:COUNT");

                }
            } catch ( \Exception $e) {
                return response('OK', 200);
            }

        }

        event(new PostViewersCount($id));

        return response('OK', 200);
    }
}
