<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\User;
use App\UserAvatar;
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

            // Save default userAvatar
            $userAvatar = new UserAvatar();
            $userAvatar['first_initial'] = strtolower(substr($user->name,0,1));
            $userAvatar['bg_color_hex'] = $userAvatar->generateColorHex();
            $userAvatar['side_color_hex'] = $userAvatar->generateColorHex();
            $userAvatar['stroke_color_hex'] = $userAvatar->generateColorHex();
            $user->userAvatar()->save($userAvatar);

            $token = auth()->login($user);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
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
                return $this->standardLoginFailedResponse();
            }

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
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
                HttpStatusCode::SUCCESS_OK,
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
                HttpStatusCode::SUCCESS_OK,
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

    public function changePassword (Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
               'old_password' => 'required|string',
               'new_password' => 'required|string|confirmed'
            ]);

            if($validator->fails())
                return $this->standardJsonValidationErrorResponse($validator->errors()->first());

            $user = User::findOrFail(auth()->user()->id);
            $user->password = $request->new_password;
            $user->save();

            // log out after password is changed
            auth()->logout();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                'Password changed successfully. Please log in again.'
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
