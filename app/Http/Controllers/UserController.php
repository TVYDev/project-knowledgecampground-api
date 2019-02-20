<?php

namespace App\Http\Controllers;

use App\Libs\JsonResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use JsonResponse;

    public function register(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|max:50',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required'
            ]);

            if($validator->fails()){
                return $this->standardJsonValidationErrorResponse($validator->errors()->first());
            }

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $request->password
            ]);

            $token = auth()->login($user);

            return $this->standardJsonResponse(
                201,
                true,
                'User created successfully',
                [
                    'access_token'  => $token,
                    'token_type'    => 'bearer',
                    'expire_in'     => auth()->factory()->getTTL() * 60
                ]);
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function login (Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'email'     => 'required|email',
                'password'  => 'required'
            ]);

            if($validator->fails()){
                return $this->standardJsonValidationErrorResponse($validator->errors()->first());
            }

            $credentials = request(['email', 'password']);

            // @-Return token if user is authenticated, otherwise return false
            $token = auth()->attempt($credentials);

            if(!$token){
                return $this->standardJsonUnauthorizedResponse();
            }

            return $this->standardJsonResponse(
                200,
                true,
                'User logs in successfully',
                [
                    'access_token'  => $token,
                    'token_type'    => 'bearer',
                    'expire_in'     => auth()->factory()->getTTL() * 60
                ]
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function logout ()
    {
        try
        {
            auth()->logout();

            return $this->standardJsonResponse(
                200,
                true,
                'User logs out successfully'
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function getUser () {
        try
        {
            $user = auth()->user();

            return $this->standardJsonResponse(
                200,
                true,
                null,
                $user
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
