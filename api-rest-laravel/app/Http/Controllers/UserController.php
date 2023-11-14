<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function pruebas(Request $request){
        return "Accion de pruebas de user controlller";
    }

    public function register(Request $request){

        /*$name=$request->input('name');
        return "register ".$name;*/

        //Recoger datos del usuario por post
        $json=$request->input('json',null);
        $params=json_decode($json);//objeto
        $params_array=json_decode($json, true);//array

        //limpiar datos
        $params_array=array_map('trim', $params_array);
        
                

        //Validar Datos
        $validate=Validator::make($params_array, [
            'name'      =>'required|alpha',
            'surname'   =>'required|alpha',
            'email'     =>'required|email|unique:users',
            'password'  =>'required'
        ]);

        if($validate->fails()){
            $data=array(
                'status'=>'error',
                'code'=>404,
                'message'=>'El usuario no se ha creado',
                'errors'=>$validate->errors()
            );
        }else{

            //Cifrar contraseña
            $pwd=Hash::make($params->password);           
            
            //crear usuario
            $user=new User();

            $user->name=$params_array['name'];
            $user->surname=$params_array['surname'];
            $user->email=$params_array['email'];
            $user->password=$pwd;
            $user->role="user";

            //guardar usuario
            $user->save();        



            $data=array(
                'status'=>'Success',
                'code'=>200,
                'message'=>'El usuario se a creado exitosamente'                
            );

        }       


       

        return response()->json($data, $data['code']);
        
    }


    public function login(Request $request){

        $jwtAuth=new JwtAuth();

        //Recibir datos por POST

        $json=$request->input('json',null);
        $params=json_decode($json);
        $params_array=json_decode($json,true);

        //Validar Datos
        
        $validate=Validator::make($params_array, [
            'email'      =>['required'],            
            'password'  =>['required']
        ]);



        if($validate->fails()){
            $signup=array(
                'status'=>'error',
                'code'=>404,
                'message'=>'El usuario no se a podido logear',
                'errors'=>$validate->errors()
            );}
            else{
                //Devolver token o datos
                $signup = $jwtAuth->singup($params->email, $params->password);

                if(isset($params->gettoken)){
                    $signup = $jwtAuth->singup($params->email, $params->password,true);
                }


            }

        return response()->json ($signup,200);
            
    }

    public function update(Request $request){

        //Comprobar si el usaurio esta identificado
        $token=$request->header('Authorization');
        $jwtAuth=new JwtAuth();
        $checktoken=$jwtAuth->checkToken($token);

        //Recoger datos por POST

        $json=$request->input('json',null);
        $params_array=json_decode($json,true);

        if($checktoken && !empty($params_array)){
           //Actualizar usuario

           //sacar usuario identificado
           $user=$jwtAuth->checkToken($token,true);

           //Validar datos

           $validate=Validator::make($params_array,[
            'name'      =>'required|alpha',
            'surname'   =>'required|alpha',
            'email'     =>'required|email|unique:users,'.$user->sub
           ]);

           //Quitar campos que no queiro actualizar

           unset($params_array['id']);
           unset($params_array['role']);
           unset($params_array['password']);
           unset($params_array['created_at']);
           unset($params_array['remember_token']);


           //Actualizar usuario en bd

           $user_udpate=User::where('id',$user->sub)->update($params_array);

           //Devolver array con resultado

           $data=array(
            'code'=>200,
            'status'=>'success',
            'user'=>$user,
            'changes'=>$params_array
           );
        }
        else{
           $data=array(
            'code'=>400,
            'status'=>'error',
            'message'=>'El usuario no esta identificado'
           );
           
        }
        return response()->json($data,$data['code']);
    }

    public function upload(){

        $data=array(
            'code'=>400,
            'status'=>'error',
            'message'=>'Error al subir imagen'
           );

           return response()->json($data,$data['code']);
        

    }

}
