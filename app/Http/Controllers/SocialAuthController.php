<?php

namespace App\Http\Controllers;

use App\Libs\KCValidate;
use App\User;
use App\UserProfile;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    public function postGoogleLogin(Request $request) {
        try {
            // --- validate inputs
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_GOOGLE_LOGIN);
            if($result !== true) return $result;

            $existingUser = User::where('email', $request->email)->first();

            $user = null;
            if(!isset($existingUser)) {
                // --- create user
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email
                ]);
            }
            else {
                $user = $existingUser;
            }

            // --- create profile
            $userProfile = new UserProfile();
            $user->userProfile()->save($userProfile);

            // --- get token of the user
            $token = auth()->login($user);
        }catch(\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
