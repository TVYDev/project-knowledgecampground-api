<?php

namespace App\Http\Controllers;

use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MiddlewareConst;
use App\Permission;
use App\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use JsonResponse;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH);
    }

    public function postCreatePermission (Request $request)
    {
        try {
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_PERMISSION);
            if($result !== true) return $result;

            $permission = Permission::create([
                'name' => strtoupper($request->name),
                'created_by' => auth()->user()->id
            ]);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                'KC_MSG_SUCCESS__PERMISSION_SAVE',
                $permission
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function getAvailablePermissions ()
    {
        try {
            $permissions = Permission::where('is_active', true)->where('is_deleted', false)->get();

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

    public function getViewPermission ($permissionId)
    {
        try {
            $permission = Permission::find($permissionId);

            if(isset($permission)) {
                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    '',
                    $permission
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

    public function postAssignPermissionsToRole (Request $request)
    {
        try {
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_PERMISSION_ASSIGN);
            if($result !== true) return $result;

            $role = Role::where('id', $request->role_id)->where('is_active', true)->where('is_deleted', false)->first();
            if(isset($role)) {
                $permissionIds = $request->permission_ids;
                foreach ($permissionIds as $permissionId) {
                    $permission = Permission::where('id', $permissionId)->where('is_active', true)->where('is_deleted', false)->first();
                    if(!isset($permission)) {
                        return $this->standardJsonResponse(
                            HttpStatusCode::ERROR_BAD_REQUEST,
                            false,
                            '',
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
                    ''
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

    public function postRemovePermissionsFromRole (Request $request)
    {
        try {
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_PERMISSION_ASSIGN);
            if($result !== true) return $result;

            $role = Role::where('id', $request->role_id)->where('is_active', true)->where('is_deleted', false)->first();
            if(isset($role)) {
                $permissionIds = $request->permission_ids;
                foreach ($permissionIds as $permissionId) {
                    $permission = Permission::where('id', $permissionId)->where('is_active', true)->where('is_deleted', false)->first();
                    if(!isset($permission)) {
                        return $this->standardJsonResponse(
                            HttpStatusCode::ERROR_BAD_REQUEST,
                            false,
                            '',
                            null,
                            ErrorCode::ERR_CODE_DATA_NOT_EXIST
                        );
                    }
                }

                $role->permissions()->detach($permissionIds);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_CREATED,
                    true,
                    ''
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
