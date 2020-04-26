<?php

namespace App\Http\Controllers;

use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MessageCode;
use App\Libs\MiddlewareConst;
use App\Role;
use App\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use JsonResponse;

    protected $inputValidator;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH);
        $this->middleware(MiddlewareConst::JWT_CLAIMS);

        $this->inputValidator = new KCValidate();
    }

    /**
     * Create User Role
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postCreateUserRole (Request $request)
    {
        try {
            /* --- Validate inputs --- */
            $this->inputValidator->doValidate($request->all(), KCValidate::VALIDATION_ROLE);

            $role = Role::create([
                'name' => $request->name,
                'created_by' => auth()->user()->id
            ]);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                MessageCode::msgSuccess('role created'),
                $role
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Retrieve Available User Roles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveAvailableUserRoles ()
    {
        try {
            $roles = Role::where('is_active', true)->where('is_deleted', false)->get();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                MessageCode::msgSuccess('user roles retrieved'),
                $roles
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * View User Role
     *
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViewUserRole ($roleId)
    {
        try {
            $role = Role::find($roleId);

            if(isset($role)) {
                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('user role viewed'),
                    $role
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('user role not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Assign User Role to User
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postAssignUserRoleToUser (Request $request)
    {
        try {
            /* --- Validate inputs --- */
            $this->inputValidator->doValidate($request->all(), KCValidate::VALIDATION_ROLE_ASSIGN);

            $role = Role::where('id', $request->role_id)->where('is_active', true)->where('is_deleted', false)->first();
            $user = User::where('id', $request->user_id)->where('is_deleted', false)->first();
            if(isset($role) && isset($user)) {
                /* --- Assign role to the user --- */
                $role->users()->attach($user->id, ['created_by' => auth()->user()->id]);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_CREATED,
                    true,
                    MessageCode::msgSuccess('user role assigned to user')
                );
            }
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('user role or user not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Remove User Role from User
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postRemoveUserRoleFromUser (Request $request)
    {
        try {
            /* --- Validate inputs --- */
            $this->inputValidator->doValidate($request->all(), KCValidate::VALIDATION_ROLE_ASSIGN);

            $role = Role::where('id', $request->role_id)->where('is_active', true)->where('is_deleted', false)->first();
            $user = User::where('id', $request->user_id)->where('is_deleted', false)->first();
            if(isset($role) && isset($user)) {
                /* --- Remove user role from user --- */
                $userId = $user->id;
                $role->users()->detach($userId);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('user role removed from user')
                );
            }
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('user role or user not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
