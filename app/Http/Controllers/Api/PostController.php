<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'title' => 'required',
            'text' => 'required',
        ]);

        $user = $request->user();
        $post = $user->posts()->create([
            'title' => $request->title,
            'text' => $request->text,
        ]);

        return response()->json($post, 201);
    }

    public function index(Request $request) {
        $sort = $request->query('sort', 'date');
        $limit = $request->query('limit', 5);
        $offset = $request->query('offset', 0);

        $query = Post::query();

        if ($sort === 'title') {
            $query->orderBy('title', $sort);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        $posts = $query->limit($limit)->offset($offset)->get();

        return response()->json($posts, 200);
    }

    public function myPosts(Request $request) {
        $sort = $request->query('sort', 'date');
        $limit = $request->query('limit', 5);
        $offset = $request->query('offset', 0);

        $query = $request->user()->posts();

        if ($sort === 'title') {
            $query->orderBy('title', $sort);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        $posts = $query->limit($limit)->offset($offset)->get();

        return response()->json($posts, 200);
    }
}
