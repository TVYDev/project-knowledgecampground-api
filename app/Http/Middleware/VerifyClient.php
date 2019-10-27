<?php

namespace App\Http\Middleware;

use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use Closure;

class VerifyClient
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
        $requestClientId = $request->header('client-id');
        $requestClientSecret = $request->header('client-secret');

        $clients = [
            config('clients.kc_client_id') => config('clients.kc_client_secret')
        ];

        if(array_key_exists($requestClientId, $clients)){
            if($clients[$requestClientId] === $requestClientSecret){
                return $next($request);
            }
        }

        return $this->standardJsonResponse(
            HttpStatusCode::ERROR_BAD_REQUEST,
            false,
            'KC_MSG_ERROR__UNKNOWN_CLIENT',
            null,
            ErrorCode::ERR_CODE_UNKNOWN_CLIENT
        );
    }
}
