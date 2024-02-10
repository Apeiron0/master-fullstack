<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Helpers\JwtAuth;
use Symfony\Component\HttpFoundation\Response;
class PostController extends Controller
{
    //
   public function __construct()
   {
    $this->middleware('apiauth',['except'=>[
        'index',
        'show',
        'getImage',
        'getPostsByCategory',
        'getPostsByUser']]);
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
    $user=$this->getIdentity($request);
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

   public function update(Request $request,$id){
    //Recoger datos por post
    $json=$request->input('json',null);
    $params_array=json_decode($json,true);

    //Verificar identidad usuario
    $user=$this->getIdentity($request);

    //comprobar si no es null
    if(!empty($params_array)){

    //Validar datos
    $validate=\Validator::make($params_array,[
        'title'=>'required',
        'content'=>'required',
        'category_id'=>'required',
        'image'=>'required',
    ]);

    if($validate->fails()){
        return response()->json([
            'code'=>400,
          'status'=>'error',
          'message'=>$validate->errors()
        ]);
    }

    //Quitar lo que no quiera acttualizar
    unset($params_array['id']);
    unset($params_array['created_at']);
    unset($params_array['updated_at']);
    unset($params_array['user_id']);
    unset($params_array['user']);

    //Actualizar registro
    $post=Post::where('id',$id)->where('user_id',$user->sub)->update($params_array);

    return response()->json([
        'code'=>200,
        'status'=>'success',
        'post'=>$post,
        'changes'=>$params_array
    ]);
    }
    else{
        return response()->json([
            'code'=>400,
           'status'=>'error',
           'message'=>'No se envian datos'
        ]);
    }
   }

   public function destroy(Request $request,$id){

        $user=$this->getIdentity($request);
        //$post=Post::find($id)->where('user_id',$user->sub);
        
        $post=Post::where('id',$id)->where('user_id',$user->sub)->delete($id);
        if($post>=1){
            return response()->json([
                'code'=>200,
                'status'=>'success',                
            ]);
        }else{
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>'Post not found'
            ]);
        }
   }

   private function getIdentity(Request $request){
        //conseguir datos usuario
        $jwtAuth=new JWTAuth();
        $token=$request->header('Authorization',null);
        $user=$jwtAuth->checkToken($token,true);

        return $user;
   }
   
   public function upload(Request $request){
    //Recoger imagen de la peticion
    $image=$request->file('file0');

    //Validar imagen
    $validate=\Validator::make($request->all(),[
        'file0'=>'required|image|mimes:jpg,jpeg,png,gif'
    ]);

    //Guardar imagen
    if(!$image||$validate->fails()){
        $data=[
            'code' =>400,
            'status' =>'error',
            'message' =>'Error al subir imagen'
        ];
    }else{
        $image_name=time().$image->getClientOriginalName();
        \Storage::disk('images')->put($image_name, \File::get($image));
        $data=[
            'code' =>200,
            'status' =>'success',
            'message' =>$image_name
        ];
    }

    //Devolver datos
    return response()->json($data,$data['code']);
   }

   public function getImage($filename){
    //Comporbar si existe el fichero
    $isset=\Storage::disk('images')->exists($filename);
    if($isset){
        //Conseguir la imagen
        $file=\Storage::disk('images')->get($filename);

        //Devolver la imagen
        return new Response($file,200);

    }else{
        //Mostrar error si es que existe
        $data=[
            'code' =>404,
            'status' =>'error',
            'message'=>'Imagen no encontrada'
        ];
    }
    return response()->json($data,$data['code']);


   }

   public function getPostsByCategory($id){
    $post=Post::where('category_id',$id)->get();
    return response()->json($post,200);
   }

   public function getPostsByUser($id){
    $post=Post::where('user_id',$id)->get();
    return response()->json($post,200);
   }
}
