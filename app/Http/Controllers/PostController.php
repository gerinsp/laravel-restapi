<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Post\PostCollection;
use DB;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        // DB::listen(function ($query) {
        //     var_dump($query->sql);
        // });
        $data = Post::with(['user'])->latest()->paginate(5);
        
        return new PostCollection($data);
        // return response()->json($data, 200);
    }

    public function show($id)
    {
        $data = Post::find($id);
        if(is_null($data)){
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return new PostResource($data);
        
        // return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'title' => 'required|min:5',
            'body' => 'required|min:5'
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $data = request()->user()->posts()->create($data);
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil ditambahkan.',
            'data' => $data
        ], 201);
    }

    public function update(Request $request, Post $post)
    {
        $post->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate.',
            'data' => $post
        ], 200);
    }

    public function destroy($id)
    {
        Post::destroy($id);
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus.',
        ], 200);
    }
}
