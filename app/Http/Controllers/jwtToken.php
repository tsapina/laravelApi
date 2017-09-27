<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;


class jwtToken extends Controller
{
    //
    public function checkIfUserExist(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
         echo "exist";
        }
    }

   


}
