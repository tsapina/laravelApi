<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use DB;
use App\User as UserModel;
use App\Role as RoleModel;
use JWTAuth;
use Auth;
use App\PermissionRoleUser as PermissionRoleUser;

class UserController extends Controller
{
    
    public function __construct(UserModel $user)
    {
        $this->user = $user;
    }

    public function getAllUsers()
    {
        try
        {
            if($this->user->all()->isEmpty()){
                return response()->json("No content", 400);
            }
            return response()->json($this->user->all(),200);
        } 
        catch(Exception $e)
        {
            return response()->json($e->getMessage(),400);
        }
    }

    public function getUserById($id)
    {
        if(!is_numeric($id)) {  return response()->json("Invalid input", 400); };
        try
        {
            if($this->user->where('id', $id)->get()->isEmpty())
            {
                return response()->json("No content", 400);
            }
            return response()->json($this->user->where('id', $id)->get(),200);
        }
        catch(Exception $e)
        {
            return response()->json($e->getMessage(),400);
        }
    }

    public function addUser(Request $request)
    {
        try
        {
             //$user->create($request->all());  isprobati
            $this->user->firstname =  $request->firstname;
            $this->user->lastname =  $request->lastname;
            $this->user->password =  Hash::make($request->password);
            $this->user->email =  $request->email;
            $this->user->adress =  $request->adress;
            $this->user->save();

          
            foreach($request->roleID as $key => $role){
                foreach($request->permissionID[$key] as $permission){
                    $data[]= array('role_id' => $role, 'permission_id' => $permission);
                }
            }
          
            $this->user->permissionsRoles()->createMany($data);
        
            return response()->json("success",200);
        }
        catch(Exception $e)
        {
            return response()->json($e->getMessage(),400);
        }
       
    }

    public function deleteUserById($id)
    {
        if(!is_numeric($id)) {  return response()->json("Invalid input", 400); };

        try
        {
           
            if($this->user->find($id) === null)
            {
                return response()->json("No content", 400);
            }
            else
            {
                $this->user->find($id)->delete();
                return response()->json("success",200);
            }
        }
        catch(Exception $e)
        {
            return response()->json($e->getMessage(),400);
        }
        
    }

    public function updateUserById(Request $request, $userId)
    {
        try
        {
            if($this->user->find($userId) === 0)
            {
                return response()->json("No user", 400);
            }
            else
            {   
                $this->user->find($userId)->update(
                    ['email' => $request->email, 
                    'firstname' => $request->firstname, 
                    'lastname' => $request->lastname, 
                    'password' => Hash::make($request->password),
                    'adress' => $request->adress]);
                
    
                $PermissionRoleUser = new PermissionRoleUser;
                $PermissionRoleUser->where('user_id', $userId)->delete();
                
                foreach($request->roleID as $key => $role){
                    foreach($request->permissionID[$key] as $permission){
                        $data[]= array('role_id' => $role, 'permission_id' => $permission);
                    }
                }    
                $this->user->find($userId)->permissionsRoles()->createMany($data);
                return response()->json("success",200);
            }
        }
        catch(Exception $e)
        {
            return response()->json($e->getMessage(),400);
        }
        
    }

    public function authenticate(Request $request)
    {   
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        if(Auth::once($credentials)) {

            $user = Auth::getUser();
            
            $PermissionRoleUser = new PermissionRoleUser;

            $usersData = $user->permissionsRoles()->with(['permission','role'])->get();

            foreach($usersData as $data)
            {
                $roleandpermissions[strtolower($data->role->name)][]= strtolower($data->permission->name);
            }

           $customFields = ['rolesandpermissions' => $roleandpermissions];

            try {
                // attempt to verify the credentials and create a token for the user
                if (!$token = JWTAuth::attempt($credentials, $customFields)) {
                    return response()->json(['error' => 'invalid_credentials'], 401);
                }
            } catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token 
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

        } else {
            // Invalid user credentials
           return response()->json("Invalid user credentials", 200);
        }
        
        // all good so return the token
        return response()->json(compact('token'));
    }

}
