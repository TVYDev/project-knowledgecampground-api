<?php


namespace App\Libs;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class FileLog
{
    public static function logRequest ($message, $success, $httpStatusCode, $errorCode) {
        try {
            /* --- Prepare data for using in the log --- */
            $inputs = Input::all(); // inputs of request
            $filteredInputs = array_filter($inputs, function($key) { // exclude password from log data
                return strpos($key, 'password') === false;
            }, ARRAY_FILTER_USE_KEY);

            /* --- Structure data for logging --- */
            $context = [
                count($filteredInputs) > 0 ? json_encode($filteredInputs) : null
            ];

            /* --- Do logging according to status of request --- */
            if($success){
                Log::info($message, $context);
            }else{
                if($httpStatusCode === HttpStatusCode::ERROR_INTERNAL_SERVER_ERROR)
                    Log::critical($errorCode.':'.$message, $context);
                else
                    Log::error($errorCode.':'.$message, $context);
            }
        }
        catch(\Exception $exception) {
            /* --- If fail to log to file, then log to slack --- */
            Log::channel('slack')->critical('Logging to file failed:== ' . $exception->getMessage());
        }
    }
}
