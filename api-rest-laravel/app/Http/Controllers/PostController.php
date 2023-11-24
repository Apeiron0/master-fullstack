<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
class PostController extends Controller
{
    //
   public function __construct()
   {
    $this->middleware('apiauth',['except'=>['index','show']]);
   }

   public function index(){
    $posts=Post::all()->load('category');
    return response()->json([
        'code'=>200,
        'status'=>'success',
        'posts'=>$posts
    ]);
   }

   public function show($id){
    $post=Post::find($id);
    if(is_object($post)){
        return response()->json([
            'code'=>200,
            'status'=>'success',
            'post'=>$post
        ]);
    }else{
        return response()->json([
            'code'=>404,
            'status'=>'error',
            'message'=>'Post not found'
        ]);
    }
   }
   
}
