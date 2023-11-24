<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Helpers\JwtAuth;
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

   public function store(Request $request){
    //Recoger datos por post
    $json=$request->input('json',null);
    $params_array=json_decode($json,true);
    
    //conseguir datos usuario
    $jwtAuth=new JWTAuth();
    $token=$request->header('Authorization',null);
    $user=$jwtAuth->checkToken($token,true);

    //validar datos
    $validate=\Validator::make($params_array,[
        'title'=>'required',
        'content'=>'required',
        'category_id'=>'required',
        'image'=>'required',
    ]);

    //guardar datos
    if($validate->fails()){
        return response()->json([
            'code'=>400,
            'status'=>'error',
            'message'=>$validate->errors()
        ]);
    }else{
        $post=new Post();
        $post->user_id=$user->sub;
        $post->category_id=$params_array['category_id'];
        $post->title=$params_array['title'];
        $post->content=$params_array['content'];
        $post->image=$params_array['image'];
        $post->save();
        return response()->json([
            'code'=>200,
            'status'=>'success',
            'post'=>$post
        ]);
    }
   }
   
}
