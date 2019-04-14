<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\User;
use App\UserAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use JsonResponse;

    /**
     * User Change Password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword (Request $request){
        try
        {
            // --- validate inputs
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'new_password' => 'required|string|confirmed'
            ]);
            $newPwd = $request->new_password;

            if(strlen($newPwd) < 8){
                return $this->standardJsonValidationErrorResponse('Password must be at least 8 characters');
            }

            if($request->old_password === $newPwd){
                return $this->standardJsonValidationErrorResponse('Old and new password cannot be the same');
            }

            if($validator->fails())
                return $this->standardJsonValidationErrorResponse($validator->errors()->first());

            // --- find user and change password for the user
            $user = User::findOrFail(auth()->user()->id);

            if(Hash::check($newPwd, $user->password1) ||
                Hash::check($newPwd, $user->password2) ||
                Hash::check($newPwd, $user->password3)){
                return $this->standardJsonValidationErrorResponse('Your new password must not be the same as your last 3 passwords');
            }
            else{
                $user->password3 = $user->password2;
                $user->password2 = $user->password1;
                $user->password1 = $user->password;
                $user->password = $newPwd;
            }
            $user->save();

            // --- log out after password is changed
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

    /**
     * User get information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser () {
        try
        {
            // --- get authenticated user
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

    /**
     * User Login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login (Request $request)
    {
        try
        {
            // --- validate inputs
            $validator = Validator::make($request->all(), [
                'email'     => 'required|email',
                'password'  => 'required'
            ]);

            if($validator->fails()){
                return $this->standardJsonValidationErrorResponse($validator->errors()->first());
            }

            // --- do login
            $credentials = request(['email', 'password']);
            $token = auth()->attempt($credentials);
                // return token if user is authenticated, otherwise return false
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

    /**
     * User Logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout ()
    {
        try
        {
            // --- do logout
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

    /**
     * User Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try
        {
            // --- validate inputs
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|max:50',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required'
            ]);

            if(strlen($request->password) < 8)
                return $this->standardJsonValidationErrorResponse('Password must be at least 8 characters');

            if($validator->fails()){
                return $this->standardJsonValidationErrorResponse($validator->errors()->first());
            }

            // --- create user
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $request->password
            ]);

            // --- create default user_avatar for the user
            $userAvatar = new UserAvatar();
            $defaultAvatar = $userAvatar->generateDefaultUserAvatar();
            $userAvatar['seed'] = $defaultAvatar['seed'];
            $userAvatar['default_avatar_url'] = $defaultAvatar['avatar_url'];
            $user->userAvatar()->save($userAvatar);

            // --- get token of the user
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
}
