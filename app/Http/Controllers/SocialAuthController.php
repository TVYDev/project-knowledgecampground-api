<?php

namespace App\Http\Controllers;

use App\Http\Support\Supporter;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MessageCode;
use App\Libs\StandardJsonFormat;
use App\Role;
use App\User;
use App\UserAvatar;
use App\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialAuthController extends Controller
{
    use JsonResponse;

    protected $inputsValidator;
    protected $supporter;

    public function __construct()
    {
        $this->inputsValidator = new KCValidate();
        $this->supporter = new Supporter();
    }

    /**
     * Login Social User
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postLoginSocialUser (Request $request) {
        try {
            DB::beginTransaction();

            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_SOCIAL_PROVIDER_LOGIN);

            $existingUser = User::where('email', $request->email)
                ->where('provider', $request->provider)
                ->first();

            $user = null;
            if(!isset($existingUser)) {
                /* --- Create user if it is not an existing user --- */
                $generatedPublicId = $this->supporter->generatePublicId();
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'provider'  => $request->provider,
                    'provider_user_id' => $request->provider_user_id,
                    'public_id' => $generatedPublicId
                ]);

                /* --- Create default user avatar for the user --- */
                $userAvatar = new UserAvatar();
                $userAvatar['default_avatar_url'] = $request->picture;
                $user->userAvatar()->save($userAvatar);

                /* --- Create user profile --- */
                $userProfile = new UserProfile();
                $user->userProfile()->save($userProfile);

                /* --- Assign user to user role "Social User" --- */
                $normalRole = Role::where('name', 'Social User')->first();
                $normalRole->users()->attach($user->id, ['created_by' => $user->id]);

                $messageCode = 'user registered';
            }
            else {
                $user = $existingUser;
                $messageCode = 'user logged in';
            }

            /* --- Get token for the user --- */
            $token = auth()->login($user);

            DB::commit();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                MessageCode::msgSuccess($messageCode),
                StandardJsonFormat::getAccessTokenFormat([$token])
            );
        }catch(\Exception $exception) {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
