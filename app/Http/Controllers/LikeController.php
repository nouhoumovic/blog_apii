<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    use ApiResponser;
    // like or unlike
    public function likeOrUnlike($id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return $this->error('Post not found', 403);
        }

        $like = $post->likes()->where('user_id', auth()->user()->id)->first();

        // if not liked then like
        if(!$like)
        {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id
            ]);

            return $this->success([],
            "Liked"
            );

        }
        // else dislike it
        $like->delete();

        return $this->success([],
            "Disliked"
        );

    }
}
