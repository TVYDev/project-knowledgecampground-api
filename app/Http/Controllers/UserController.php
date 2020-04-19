<?php

namespace App\Http\Controllers;

use App\Http\Support\Supporter;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MessageCode;
use App\Libs\MiddlewareConst;
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

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH, ['except' => [
            'postLoginUser',
            'postRegisterUser',
            'postRefreshAccessToken',
            'postSendMailLinkResetUserPassword'
        ]]);

        $this->middleware(MiddlewareConst::JWT_CLAIMS, ['except' => [
            'postLoginUser',
            'postRegisterUser',
            'postRefreshAccessToken',
            'postSendMailLinkResetUserPassword'
        ]]);
    }

    /**
     * Change User Password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postChangeUserPassword (Request $request){
        try
        {
            /* --- Validate inputs --- */
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_USER_CHANGE_PASSWORD);
            if($result !== true) return $result;

            $user = auth()->user();

            $currentPwd = $request->current_password;
            $newPwd = $request->new_password;
            /* --- Compare with current password --- */
            if(!Hash::check($currentPwd, $user->password)){
                return $this->standardJsonValidationErrorResponse('KC_MSG_ERROR__CURRENT_PASSWORD_NOT_CORRECT');
            }
            /* --- Compare with last three passwords --- */
            else if(Hash::check($newPwd, $user->password1) ||
                Hash::check($newPwd, $user->password2) ||
                Hash::check($newPwd, $user->password3)){
                return $this->standardJsonValidationErrorResponse('KC_MSG_ERROR__NEW_PASSWORD_SAME_LAST_THREE');
            }
            /* --- Update current and last three passwords accordingly --- */
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
     * Retrieve Authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveAuthenticatedUser () {
        try
        {
            /* --- get authenticated user --- */
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
     * Login User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLoginUser (Request $request)
    {
        try
        {
            /* --- Validate inputs --- */
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_USER_LOGIN);
            if($result !== true) return $result;

            /* --- Retrieve user --- */
            $user = User::where('email', $request->email)->first();

            /* --- Check if it is admin user, then abort login process --- */
            if($user->is_internal === true) {
                return $this->standardLoginFailedResponse();
            }

            /* --- Attempt to login the user --- */
            /*
             * If user is authenticated token is returned, otherwise false is given
            */
            $credentials = request(['email', 'password']);
            $token = auth()->attempt($credentials);

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
     * Logout User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLogoutUser ()
    {
        try
        {
            /* --- Logout the user */
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
     * Register User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRegisterUser (Request $request)
    {
        try
        {
            DB::beginTransaction();

            /* --- Validate inputs --- */
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_USER_REGISTER);
            if($result !== true) return $result;

            /* --- Create user --- */
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $request->password
            ]);

            /* --- Create default user avatar for the user --- */
            $userAvatar = new UserAvatar();
            $defaultUserAvatar = $userAvatar->generateDefaultUserAvatar();
            $user->userAvatar()->save($defaultUserAvatar);

            /* --- Create user profile --- */
            $userProfile = new UserProfile();
            $user->userProfile()->save($userProfile);

            /* --- Assign user to role "Normal User" --- */
            $normalRole = Role::where('name', 'Normal User')->first();
            $normalRole->users()->attach($user->id, ['created_by' => $user->id]);

            DB::commit();

            /* --- Get access token for the user --- */
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
     * Verify User Authentication
     *
     * We already have middleware outside to check the token whether it is valid or not
     * if it is not valid, exception will be thrown to exception handler, pass the JSON and do not get here
     * otherwise it gets through here and it means the token is valid.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerifyUserAuthentication () {
        return $this->standardJsonResponse(
            HttpStatusCode::SUCCESS_OK,
            true,
            'KC_MSG_SUCCESS__USER_IS_AUTHENTICATED'
        );
    }

    /**
     * Refresh Access Token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRefreshAccessToken () {
        try
        {
            /* --- Refresh access token --- */
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

    /**
     * Retrieve User Permissions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveUserPermissions ()
    {
        try {
            /* --- Query to get all permissions of the user --- */
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

    /**
     * Send Mail Link Reset User Password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postSendMailLinkResetUserPassword (Request $request) {
        try {
            $email = $request->email;
            $route = $request->route;

            /* --- Retrieve user which is not an admin user or social user --- */
            $user = User::where('email', $email)
                ->where('is_internal', false)
                ->whereNull('provider')
                ->first();

            if(isset($user)) {
                /* --- Set custom claim of access reset password to the token --- */
                $token = auth()->claims([User::KEY_JWT_CLAIM_ACCESS => User::JWT_CLAIM_ACCESS_RESET])
                    ->setTTL(1440)
                    ->tokenById($user->id);

                /* --- Attach the token to the UI link reset password provided by the client --- */
                $linkTobeSent = $route . '?token=' . $token;

                /* --- Attempt to send the mail link reset password --- */
                $result = (new Supporter())->sendEmailResetPassword($email, $linkTobeSent);
                if ($result === true) {
                    /* --- If mail sent successfully, then records the reset password request --- */
                    PasswordReset::create([
                        'email' => $email,
                        'token' => $token
                    ]);

                    return $this->standardJsonResponse(
                        HttpStatusCode::SUCCESS_OK,
                        true,
                        MessageCode::msgSuccess('reset_mail_sent')
                    );
                }
                return $this->standardJsonResponse(
                    HttpStatusCode::ERROR_REQUEST_TIMEOUT,
                    false,
                    MessageCode::msgError('reset_mail_sent'),
                    ErrorCode::ERR_CODE_SEND_MAIL_FAILED
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('user_not_exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Reset User Password
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postResetUserPassword (Request $request)
    {
        try {
            /* --- Validate inputs --- */
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_RESET_PASSWORD);
            if($result !== true) return $result;

            $userId = auth()->user()->id;
            $user = User::find($userId);

            /* --- Get bearer token from the request header --- */
            $bearerToken = Supporter::getBearerToken($request->header('Authorization'));

            /* --- Check if reset password request is recorded. If so, then continue the reset password process --- */
            $passwordReset = PasswordReset::where('email', $user->email)->where('token', $bearerToken)->first();
            if(isset($passwordReset)) {
                /* --- Update user current password with the new password --- */
                $user->password = $request->new_password;
                $user->save();

                /* --- Send mail successfully reset user password --- */
                (new Supporter())->sendEmailSuccessfulResetPassword($user->email);

                /* --- Logout current user, purpose to invalid the token for this reset password process --- */
                auth()->logout();

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('password_reset')
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('invalid_link'),
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
