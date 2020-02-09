<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\User;
use App\UserAvatar;
use App\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                return $this->standardJsonValidationErrorResponse('KC_MSG_ERROR__CURRENT_PASSWORD_NOT_CORRECT');
            }
            else if(Hash::check($newPwd, $user->password1) ||
                Hash::check($newPwd, $user->password2) ||
                Hash::check($newPwd, $user->password3)){
                return $this->standardJsonValidationErrorResponse('KC_MSG_ERROR__NEW_PASSWORD_SAME_LAST_THREE');
            }
            else{
                $user->password3 = $user->password2;
                $user->password2 = $user->password1;
                $user->password1 = $user->password;
                $user->password = $newPwd;
            }
            $user->save();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                'KC_MSG_SUCCESS__USER_CHANGE_PASSWORD'
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
                'KC_MSG_SUCCESS__USER_LOGIN',
                [
                    'access_token'  => $token,
                    'token_type'    => 'bearer',
                    'expire_in'     => auth()->factory()->getTTL() * 60 . ' seconds'
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
                'KC_MSG_SUCCESS__USER_LOGOUT'
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

            DB::beginTransaction();

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

            // --- create profile
            $userProfile = new UserProfile();
            $user->userProfile()->save($userProfile);

            DB::commit();

            // --- get token of the user
            $token = auth()->login($user);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                'KC_MSG_SUCCESS__USER_REGISTER',
                [
                    'access_token'  => $token,
                    'token_type'    => 'bearer',
                    'expire_in'     => auth()->factory()->getTTL() * 60 . ' seconds'
                ]);
        }
        catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * We already have middleware outside to check the token whether it is valid or not
     * if it is not valid, exception will be thrown to exception handler, pass the JSON and do not get here
     * otherwise it gets through here and it means the token is valid.
     */
    public function verifyAuthentication () {
        return $this->standardJsonResponse(
            HttpStatusCode::SUCCESS_OK,
            true,
            'KC_MSG_SUCCESS__USER_IS_AUTHENTICATED'
        );
    }

    public function refreshToken () {
        try
        {
            $newToken = auth()->refresh();
            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                null,
                [
                    'access_token'  => $newToken,
                    'token_type'    => 'bearer',
                    'expire_in'     => auth()->factory()->getTTL() * 60 . ' seconds'
                ]
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
