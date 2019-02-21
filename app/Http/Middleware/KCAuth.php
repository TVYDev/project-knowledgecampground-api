<?php

namespace App\Http\Middleware;

use App\TrashToken;
use App\Libs\ErrorCode;
use App\Libs\JsonResponse;
use Closure;

class KCAuth
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
        $token = $request->bearerToken();
        $trashToken = TrashToken::where('token', $token)->first();
        if($trashToken)
            return $this->standardJsonResponse(
                400,
                false,
                'Invalid Token',
                null,
                ErrorCode::ERR_CODE_TOKEN_TRASHED
            );

        return $next($request);
    }
}
