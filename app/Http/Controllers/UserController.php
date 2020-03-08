<?php

namespace App\Http\Controllers;

use App\Http\Support\Supporter;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\PasswordReset;
use App\Role;
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

            $user = User::where('email', $request->email)->first();
            if($user->is_internal === true) {
                return $this->standardLoginFailedResponse();
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

            // --- assign to role Normal User
            $normalRole = Role::where('name', 'Normal User')->first();
            $normalRole->users()->attach($user->id, ['created_by' => $user->id]);

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

    public function getUserPermissions ()
    {
        try {
            $permissions = DB::table('permissions AS p')
                ->select(DB::raw('DISTINCT p.*'))
                ->join('role_permission_mappings AS rpm', 'rpm.permission__id', '=', 'p.id')
                ->join('roles AS r', 'r.id', '=', 'rpm.role__id')
                ->join('user_role_mappings AS urm', 'urm.role__id', '=', 'r.id')
                ->join('users AS u', 'u.id', '=', 'urm.user__id')
                ->where('p.is_active', '=', true)
                ->where('p.is_deleted', '=', false)
                ->where('rpm.is_active', '=', true)
                ->where('rpm.is_deleted', '=', false)
                ->where('r.is_active', '=', true)
                ->where('r.is_deleted', '=', false)
                ->where('urm.is_active', '=', true)
                ->where('urm.is_deleted', '=', false)
                ->where('u.is_active', '=', true)
                ->where('u.is_deleted', '=', false)
                ->where('u.id', '=', auth()->user()->id)
                ->get();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                '',
                $permissions
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function postSendResetEmail (Request $request) {
        try {
            $email = $request->email;
            $route = $request->route;

            $user = User::where('email', $email)
                ->where('is_internal', false)
                ->whereNull('provider')
                ->first();

            if(isset($user)) {
                $token = auth()->claims([User::KEY_JWT_CLAIM_ACCESS => User::JWT_CLAIM_ACCESS_RESET])
                    ->setTTL(1440)
                    ->tokenById($user->id);

                $linkTobeSent = $route . '?token=' . $token;

                $result = (new Supporter())->sendEmailResetPassword($email, $linkTobeSent);
                if ($result === true) {
                    PasswordReset::create([
                        'email' => $email,
                        'token' => $token
                    ]);

                    return $this->standardJsonResponse(
                        HttpStatusCode::SUCCESS_OK,
                        true,
                        ''
                    );
                }
                return $this->standardJsonResponse(
                    HttpStatusCode::ERROR_REQUEST_TIMEOUT,
                    false,
                    '',
                    ErrorCode::ERR_CODE_SEND_MAIL_FAILED
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                '',
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
