<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use DB;
use App\User as UserModel;
use JWTAuth;
use Auth;
use App\Role as RoleModel;

class UserController extends Controller
{
    //TODO: refakturirati cijeli kod da se koriste property sa usera
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
            $password = Hash::make($request->password);
            $roleID = $request ->roleID;

            $this->user->firstname =  $request->firstname;
            $this->user->lastname =  $request->lastname;
            $this->user->password =  $password;
            $this->user->email =  $request->email;
            $this->user->adress =  $request->adress;
            $this->user->save();
            $this->user->roles()->attach($roleID);
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
                $password = Hash::make($request->password);
                $roleID = $request ->roleID;
                
                $this->user->find($userId)->update(
                    ['email' => $request->email, 
                    'firstname' => $request->firstname, 
                    'lastname' => $request->lastname, 
                    'password' => $password, 
                    'adress' => $request->adress]);
            
                    $this->user->find($userId)->roles()->sync($request->roleID);
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
            $role = new RoleModel;
            $permissions = array();

            //$user->find(1)->with(['roles.permissions'])->first()->toArray()

            //get all permissions by user
            foreach($user->roles->toArray() as $array){
                foreach($role->find($array['id'])->permissions->toArray() as $permission)
                {
                    if(!in_array($permission['name'], $permissions)){
                        $permissions[] = $permission['name'];
                    }
                } 
            }

            $customClaims = ['permissions' => $permissions];

    
            try {
                // attempt to verify the credentials and create a token for the user
                if (!$token = JWTAuth::attempt($credentials,$customClaims)) {
                    return response()->json(['error' => 'invalid_credentials'], 401);
                }
            } catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
           
          
          
        } else {
            // Invalid user credentials
        }
        
       


        // all good so return the token
        return response()->json(compact('token'));
    }

}
