<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('apiauth',['except'=>['index','show']]);
    }
    //
    public function index(){
        $categories=Category::all();
        return response()->json([
            'code'=>200,
            'status'=>'success',
            'categories'=>$categories
        ]);
    }

    public function show($id){
        $category=Category::find($id);
        if(is_object($category)){
            return response()->json([
                'code'=>200,
              'status'=>'success',
                'category'=>$category
            ]);
        }else{
            return response()->json([
                'code'=>404,
              'status'=>'error',
              'message'=>'Category not found'
            ]);
        }
        
    
    }

    public function store(Request $request){
        //Recoger datos por post
        $json=$request->input('json',null);
        $params_array=json_decode($json,true);

        if(!empty($params_array)){

        //Validar datos
        $validate=\Validator::make($params_array,[
            'name'=>'required'
        ]);

        //Guardar la categoria
        if($validate->fails()){
            return response()->json([
                'code'=>400,
              'status'=>'error',
              'message'=>$validate->errors()
            ]);
        }else{
            $category=new Category();
            $category->name=$params_array['name'];
            $category->save();
            return response()->json([
                'code'=>200,
              'status'=>'success',
                'category'=>$category
            ]);
        }
        }else{
            return response()->json([
                'code'=>400,
             'status'=>'error',
             'message'=>'No se envian datos'
            ]);
        }

        

    }
}
