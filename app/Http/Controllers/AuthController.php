<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->guard = "api"; // add
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Please check your email or password', 'status' => false], 401);
        }

        return $this->respondWithToken($token);
    }

     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'email' => auth()->user()->email,
            'token' => $token,
            'username' => auth()->user()->username
        ]);
    }

    public function register(Request $request) {
    
        //validate incoming request 
        $validator = Validator::make($request->user,[
            'username' => 'required|string|unique:users',
            'password' => 'required',
            'name' => 'required',
            'email' => 'required|string|unique:users',
            'phone' => 'required|string|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ]);
        }

        DB::beginTransaction();
        try {
            $user = new User;
            $user->name = $request->user['name'];
            $user->username = $request->user['username'];
            $user->email = $request->user['email'];
            $user->phone = $request->user['phone'];
            $user->country = $request->user['country'];
            $user->city = $request->user['city'];
            $user->postcode = $request->user['postcode'];
            $user->address = $request->user['address'];
            
            // $password = $request->input('password');
            // $user->password = app('hash')->make($password);
            $password = $request->user['password'];
            $user->password = app('hash')->make($password);
            $user->save();
            DB::commit();

            $data = array(
                'email' => $user->email,
                'username' => $user->username,
                'token' => 'For token, you can login to get that'
            );

            //return successful response
            return response()->json($data, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            //return error message
            return response()->json(['message' => 'User Registration Failed!', 'error' => $e->getMessage() ], 409);
        }

    }


}
