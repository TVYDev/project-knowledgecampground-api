<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\User;
use App\UserAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_USER_CHANGE_PASSWORD);
            if($result !== true) return $result;

            $user = auth()->user();

            $currentPwd = $request->current_password;
            $newPwd = $request->new_password;
            if(!Hash::check($currentPwd, $user->password)){
                return $this->standardJsonValidationErrorResponse('Your current password is not correct');
            }
            else if(Hash::check($newPwd, $user->password1) ||
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
                'Password is changed successfully. Please log in again.'
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
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_USER_LOGIN);
            if($result !== true) return $result;

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
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_USER_REGISTER);
            if($result !== true) return $result;

            // --- create user
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $request->password
            ]);

            // --- create default user_avatar for the user
            $userAvatar = new UserAvatar();
            $defaultUserAvatar = $userAvatar->generateDefaultUserAvatar();
            $user->userAvatar()->save($defaultUserAvatar);

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
