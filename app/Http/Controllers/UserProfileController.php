<?php

namespace App\Http\Controllers;

use App\Country;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\MessageCode;
use App\Libs\MiddlewareConst;
use App\User;
use App\UserAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{
    use JsonResponse;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH);
        $this->middleware(MiddlewareConst::JWT_CLAIMS);
    }

    /**
     * Update User Profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpdateUserProfile (Request $request)
    {
        try
        {
            DB::beginTransaction();

            $user = User::find(auth()->user()->id);
            $userProfile = $user->userProfile()->where('is_active', true)->where('is_deleted', false)->first();

            if(isset($userProfile))
            {
                $country = Country::where('code', $request->country_code)->where('is_active', true)->first();
                $countryId = null;
                if(isset($country)) {
                    $countryId = $country->id;
                }

                $userProfile->update([
                    'full_name' => $request->full_name,
                    'location'  => $request->location,
                    'position'  => $request->position,
                    'about_me'  => $request->about_me,
                    'website_link'  => $request->website_link,
                    'facebook_link' => $request->facebook_link,
                    'twitter_link'  => $request->twitter_link,
                    'telegram_link' => $request->telegram_link,
                    'country__id'   => $countryId
                ]);

                if($request->has('avatar_type')) {
                    $userAvatar = $user->userAvatar;
                    if($request->avatar_type == 'image') {
                        if($request->hasFile('img_upload') && $request->has('img_file_name'))
                        {
                            $request->img_upload->storeAs('public/user_images', $request->img_file_name);

                            $userAvatar['is_using_default'] = false;
                            $userAvatar['img_url'] = $request->img_file_name;
                        }
                        else {
                            $userAvatar['is_using_default'] = true;
                        }
                    }
                    else {
                        $userAvatar['is_using_default'] = true;
                    }
                    $userAvatar->save();
                }

                DB::commit();

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('user profile updated'),
                    $userProfile
                );
            }
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('user profile not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * View User Profile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViewUserProfile ()
    {
        try
        {
            $user = User::find(auth()->user()->id);
            if(isset($user)) {
                $userProfile = $user->userProfile()->where('is_active', true)->where('is_deleted', false)->first();
                if(isset($userProfile)) {
                    $userProfile->country;
                    $userAvatar = new UserAvatar();
                    $userProfile['avatar_url'] = $userAvatar->getActiveUserAvatarUrl($user);
                    $userProfile['avatar_url_jdenticon'] = $userAvatar->getActiveUserAvatarUrl($user, true);
                    $userProfile['username'] = $userProfile->user()->pluck('name')->first();
                    return $this->standardJsonResponse(
                        HttpStatusCode::SUCCESS_OK,
                        true,
                        MessageCode::msgSuccess('user profile viewed'),
                        $userProfile
                    );
                }
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('user profile not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
