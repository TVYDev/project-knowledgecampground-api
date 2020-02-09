<?php

namespace App\Http\Controllers;

use App\Country;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\User;
use App\UserAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{
    use JsonResponse;

    public function postUpdate (Request $request)
    {
        try
        {
            $user = User::find(auth()->user()->id);
            $userProfile = $user->userProfile()->where('is_active', true)->where('is_deleted', false)->first();

            if(isset($userProfile))
            {
                DB::beginTransaction();

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

                if($request->hasFile('img_upload') && $request->has('img_file_name'))
                {
                    $request->img_upload->storeAs('public/user_images', $request->img_file_name);

                    $userAvatar = $user->userAvatar;
                    $userAvatar['is_using_default'] = false;
                    $userAvatar['img_url'] = $request->img_file_name;
                    $userAvatar->save();
                }

                DB::commit();

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    '',
                    $userProfile
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
        catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function getView ()
    {
        try
        {
            $user = User::find(auth()->user()->id);
            if(isset($user)) {
                $userProfile = $user->userProfile()->where('is_active', true)->where('is_deleted', false)->first();
                if(isset($userProfile)) {
                    $userProfile->country;
                    $userProfile['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($user);
                    $userProfile['username'] = $userProfile->user()->pluck('name')->first();
                    return $this->standardJsonResponse(
                        HttpStatusCode::SUCCESS_OK,
                        true,
                        '',
                        $userProfile
                    );
                }
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                '',
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
