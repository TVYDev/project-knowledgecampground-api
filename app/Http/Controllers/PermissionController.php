<?php

namespace App\Http\Controllers;

use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MessageCode;
use App\Libs\MiddlewareConst;
use App\Permission;
use App\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use JsonResponse;

    protected $inputsValidator;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH);
        $this->middleware(MiddlewareConst::JWT_CLAIMS);

        $this->inputsValidator = new KCValidate();
    }

    /**
     * Create User Permission
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postCreateUserPermission (Request $request)
    {
        try {
            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_PERMISSION);

            $permission = Permission::create([
                'name' => strtoupper($request->name),
                'created_by' => auth()->user()->id
            ]);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                MessageCode::msgSuccess('permission created'),
                $permission
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Retrieve Available User Permissions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveAvailableUserPermissions ()
    {
        try {
            $permissions = Permission::where('is_active', true)->where('is_deleted', false)->get();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                MessageCode::msgSuccess('user permissions retrieved'),
                $permissions
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * View User Permission
     *
     * @param $permissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViewUserPermission ($permissionId)
    {
        try {
            $permission = Permission::find($permissionId);

            if(isset($permission)) {
                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('user permission viewed'),
                    $permission
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('user permission not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Assign User Permissions to User Role
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postAssignUserPermissionsToUserRole (Request $request)
    {
        try {
            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_PERMISSION_ASSIGN);

            $role = Role::where('id', $request->role_id)->where('is_active', true)->where('is_deleted', false)->first();
            if(isset($role)) {
                $permissionIds = $request->permission_ids;
                foreach ($permissionIds as $permissionId) {
                    $permission = Permission::where('id', $permissionId)->where('is_active', true)->where('is_deleted', false)->first();
                    if(!isset($permission)) {
                        return $this->standardJsonResponse(
                            HttpStatusCode::ERROR_BAD_REQUEST,
                            false,
                            MessageCode::msgError('user permission not exist'),
                            null,
                            ErrorCode::ERR_CODE_DATA_NOT_EXIST
                        );
                    }
                }

                $tmpArrayValues = [];
                for ($i=0; $i<count($permissionIds); $i++) {
                    array_push($tmpArrayValues, ['created_by' => auth()->user()->id]);
                }
                $dataPermissions = array_combine($permissionIds, $tmpArrayValues);
                $role->permissions()->attach($dataPermissions);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_CREATED,
                    true,
                    MessageCode::msgSuccess('user permissions assigned to user role')
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
     * Remove User Permissions from User Role
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postRemoveUserPermissionsFromUserRole (Request $request)
    {
        try {
            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_PERMISSION_ASSIGN);

            $role = Role::where('id', $request->role_id)->where('is_active', true)->where('is_deleted', false)->first();
            if(isset($role)) {
                $permissionIds = $request->permission_ids;
                foreach ($permissionIds as $permissionId) {
                    $permission = Permission::where('id', $permissionId)->where('is_active', true)->where('is_deleted', false)->first();
                    if(!isset($permission)) {
                        return $this->standardJsonResponse(
                            HttpStatusCode::ERROR_BAD_REQUEST,
                            false,
                            MessageCode::msgError('user permission not exist'),
                            null,
                            ErrorCode::ERR_CODE_DATA_NOT_EXIST
                        );
                    }
                }

                $role->permissions()->detach($permissionIds);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_CREATED,
                    true,
                    MessageCode::msgSuccess('user permissions removed from user role')
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
}
