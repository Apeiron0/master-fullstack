<?php
namespace App\Helpers;

//use Firebase\JWT\JWT;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\Auth;

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key='esto es una clave super secreta-66358';

    }

    public function singup($email, $password, $getToken=null){

    $credentials=[
        'email'=>$email,
        'password'=>$password
    ];
    
    $signup=false;

    if (Auth::attempt($credentials)) {

        $user=User::where([
            'email'=>$email,
            //'password'=>$password
        ])->first();   
    
        //comporbar si las credenciales son correctas
    
        
    
        if(is_object($user)){
    
            $signup=true;
    
        }
    }


    //Si existe el usuario con sus credenciales
    

    //Generar token con datos de usuario identidficado

    if($signup){
        $token=array(
            'sub'       =>$user->id,
            'email'     =>$user->email,
            'name'      =>$user->name,
            'surname'   =>$user->surname,
            'iat'       =>time(),
            'exp'       =>time()+(7*24*60*60)
        );

        $jwt=JWT::encode($token,$this->key,'HS256');   
        
        $decoded=JWT::decode($jwt,$this->key,['HS256']);
        //Devolver los datos decodificados o el token en funcion de un parametro
        if(is_null($getToken)){
            $data=$jwt;            
        }else{
            $data=$decoded;

        }

    }else{
        $data=array(
            'status'    =>'error',
            'message'   =>'Login incorrecto'
        );
    }

    


    
    return $data;

    }

    public function checkToken($jwt, $getIdenitty=false){

        $auth=false;

        try {
            //code...
            $jwt=str_replace('"','',$jwt);
            $decoded=JWT::decode($jwt,$this->key,['HS256']);

        } catch (\UnexpectedValueException $e) {
            //throw $th;
            $auth=false;
        }
        catch(\DomainException){
            $auth=false;
        }

        if(!empty($decoded)&&is_object($decoded)&&isset($decoded->sub)){
            $auth=true;
        }else{
            $auth=false;
        }
        if($getIdenitty){
            return $decoded;
        }
        return $auth;
        
    }


    
}