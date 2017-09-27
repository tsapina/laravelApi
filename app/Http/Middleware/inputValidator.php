<?php

namespace App\Http\Middleware;

use Validator;
use Closure;

class inputValidator
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
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string| max:20',
            'lastname' => 'required|string| max:20',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'adress' => 'required|max: 35',
            'roleID' => 'required|array',
            "roleID.*" => 'required|distinct|numeric',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        else
        {
            return $next($request);
        }
    }
}
