<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\GetUserFromToken;
use JWTAuth;
use Closure;
use App\User as UserModel;
use App\Role as RoleModel;

class checkPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
          
            $rules= [
                    'user' =>[
                        'DELETE' => ['superadministrator','administrator'],
                        'GET'=> ['superadministrator','administrator', 'employee'],
                        'PUT'=>['superadministrator','administrator'],
                        'POST'=>['superadministrator']
                    ]
                    ];
        try {
                if (!$user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
                }
                else
                {       
                        $userRolesAndPermissions = JWTAuth::getPayload()->get('rolesandpermissions');
                        
                        $routeMethods = $request->method();

                        //get resoursce name from controller
                        $action = $request->route()->action;
                        $controller = $action['controller'];
                        $explode1 = explode("@", $controller);
                        $explode2 = explode('\\', $explode1[0]);
                        $controllerName = end($explode2);
                        $resoursceName = strtolower (str_replace("Controller", '', $controllerName));

                        
                        //set perrmission to check
                        if($routeMethods === "GET"){
                                $permissionToCheck = "read";
                        }else if($routeMethods === "POST"){
                                $permissionToCheck = "create";
                        }else if($routeMethods === "DELETE") {
                                $permissionToCheck = "delete";
                        }else{
                                $permissionToCheck = "update";
                        }
                   

                        $canAccess = false;
                        foreach($rules[$resoursceName][$routeMethods] as $role)
                        {
                                //check if role exist
                               if(array_key_exists($role, $userRolesAndPermissions)){
                                       //check if permission exist
                                       if(in_array($permissionToCheck , $userRolesAndPermissions[$role])){ 
                                                $canAccess = true;
                                       }
                               }
                        }   
                        
                        if($canAccess)
                        {
                                 return $next($request); 
                        }
                        else 
                        {
                                return response()->json("You dont have perrmission", 200);
                        }
                }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json(['token_absent'], $e->getStatusCode());
        }


    }

}
