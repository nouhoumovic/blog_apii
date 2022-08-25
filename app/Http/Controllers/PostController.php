<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponser;
     public function index()
    {
        // return $this->success([
        //     'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
        //     ->with('likes', function($like){
        //         return $like->where('user_id', auth()->user()->id)
        //             ->select('id', 'user_id', 'post_id')->get();
        //     })
        //     ->get()
        // ]);

        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
            ->with('likes', function($like){
                return $like->where('user_id', auth()->user()->id)
                    ->select('id', 'user_id', 'post_id')->get();
            })
            ->get()
        ], 200);

    }
    public function show($id)
    {
        // return $this->success([
        //     'posts' => Post::where('id',$id)->withCount('comments','likes')->get()
        // ]);

        return response([
            'post' => Post::where('id', $id)->withCount('comments', 'likes')->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $attr = $request->validate([
            'body' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'posts');

        $post = Post::create([
            'body' => $attr['body'],
            'user_id' => auth()->user()->id,
              'image' => $image
        ]);

        // return $this->success([
        //    'post'=>$post
        // ],
        // "Post created."
        // );

        return response([
            'message' => 'Post created.',
            'post' => $post,
        ], 200);
    }

    public function update(Request $request,$id)
    {
        $post =Post::find($id);

        if(!$post)
        {
            return $this->error('Post not found', 403);
        }

        if($post->user_id != auth()->user()->id)
        {
            return $this->error('Permission denied', 403);
        }
        $attr = $request->validate([
            'body' => 'required|string',
        ]);

        $post->update([
            'body' => $attr['body'],
        ]);

        // return $this->success([
        //    'post'=>$post
        // ],
        // "Post update."
        // );

        return response([
            'message' => 'Post updated.',
            'post' => $post
        ], 200);
    }

    public function destroy($id)
    {
        $post =Post::find($id);

        if(!$post)
        {
            return $this->error('Post not found', 403);
        }

        if($post->user_id != auth()->user()->id)
        {
            return $this->error('Permission denied', 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        // return $this->success([],
        //  "Post deleted."
        //  );

         return response([
            'message' => 'Post deleted.'
        ], 200);
    }
}
