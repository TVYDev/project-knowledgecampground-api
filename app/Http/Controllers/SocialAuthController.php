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

class SocialAuthController extends Controller
{
    use JsonResponse;

    public function postGoogleLogin(Request $request) {
        try {
            DB::beginTransaction();

            // --- validate inputs
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_GOOGLE_LOGIN);
            if($result !== true) return $result;

            $existingUser = User::where('email', $request->email)->first();

            $user = null;
            if(!isset($existingUser)) {
                // --- create user
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'google_id' => $request->google_id
                ]);

                // --- create default user_avatar for the user
                $userAvatar = new UserAvatar();
                $userAvatar['default_avatar_url'] = $request->picture;
                $user->userAvatar()->save($userAvatar);

                // --- create profile
                $userProfile = new UserProfile();
                $user->userProfile()->save($userProfile);
            }
            else {
                $user = $existingUser;
            }

            // --- get token of the user
            $token = auth()->login($user);

            DB::commit();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                'KC_MSG_SUCCESS__USER_REGISTER',
                [
                    'access_token'  => $token,
                    'token_type'    => 'bearer',
                    'expire_in'     => auth()->factory()->getTTL() * 60 . ' seconds'
                ]);
        }catch(\Exception $exception) {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
