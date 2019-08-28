<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use JsonResponse;

    public function getAllSubjects ()
    {
        try
        {
            $subjects = Subject::where('is_active', true)->get();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                null,
                $subjects
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
