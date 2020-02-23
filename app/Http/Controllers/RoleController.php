<?php

namespace App\Http\Controllers;

use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MiddlewareConst;
use App\Role;
use App\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use JsonResponse;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH);
    }

    public function postCreateRole (Request $request)
    {
        try {
            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_ROLE);
            if($result !== true) return $result;

            $role = Role::create([
                'name' => $request->name,
                'created_by' => auth()->user()->id
            ]);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                'KC_MSG_SUCCESS__ROLE_SAVE',
                $role
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function getAvailableRoles ()
    {
        try {
            $roles = Role::where('is_active', true)->where('is_deleted', false)->get();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                '',
                $roles
            );
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function getViewRole ($roleId)
    {
        try {
            $role = Role::find($roleId);

            if(isset($role)) {
                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    '',
                    $role
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

    public function postAssignRoleToUser (Request $request)
    {
        try {
            // -- validate input
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_ROLE_ASSIGN);
            if($result !== true) return $result;

            $role = Role::where('id', $request->role_id)->where('is_active', true)->where('is_deleted', false)->first();
            $user = User::where('id', $request->user_id)->where('is_deleted', false)->first();
            if(isset($role) && isset($user)) {
                $role->users()->attach($user->id, ['created_by' => auth()->user()->id]);

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

    public function postRemoveRoleFromUser (Request $request)
    {
        try {
            // -- validate input
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_ROLE_ASSIGN);
            if($result !== true) return $result;

            $role = Role::where('id', $request->role_id)->where('is_active', true)->where('is_deleted', false)->first();
            $user = User::where('id', $request->user_id)->where('is_deleted', false)->first();
            if(isset($role) && isset($user)) {
                $userId = $user->id;
                $role->users()->detach($userId);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
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
