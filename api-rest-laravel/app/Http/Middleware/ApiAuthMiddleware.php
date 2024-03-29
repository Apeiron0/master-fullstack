<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Helpers\JwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
          //Comprobar si el usaurio esta identificado
        $token=$request->header('Authorization');
        $jwtAuth=new JwtAuth();
        $checktoken=$jwtAuth->checkToken($token);

        if($checktoken){
            return $next($request);
        }else{
            $data=array(
             'code'=>400,
             'status'=>'error',
             'message'=>'El usuario no esta identificado'
            );
            return response()->json($data, $data['code']);
            
         }
        
    }
}
