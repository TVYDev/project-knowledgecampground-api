<?php

namespace App\Http\Middleware;

use App\Http\Support\Supporter;
use App\Libs\JsonResponse;
use App\Libs\RouteConst;
use App\User;
use Closure;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class VerifyBearerToken
{
    use JsonResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $payloads = auth()->payload();
            $access = $payloads[User::KEY_JWT_CLAIM_ACCESS];
            if(isset($access)) {
                if($access === User::JWT_CLAIM_ACCESS_GENERAL) {
                    return $next($request);
                }
                elseif ($access === User::JWT_CLAIM_ACCESS_RESET) {
                    $incomingRouteName = $request->route()->getName();
                    if($incomingRouteName === RouteConst::USER_POST_SEND_RESET_EMAIL) {
                        return $next($request);
                    }
                }
            }
            throw new TokenInvalidException('Invalid Token');
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
