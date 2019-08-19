<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use Illuminate\Http\Request;

class QuestionDescriptionController extends Controller
{
    use JsonResponse;

    public function postSave(Request $request)
    {
        try
        {
            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_QUESTION_DESCRIPTION_SAVE);
            if($result !== true) return $result;

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                null,
                $request->desc_data
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
