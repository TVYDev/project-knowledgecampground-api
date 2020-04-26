<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\MessageCode;
use App\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use JsonResponse;

    /**
     * Retrieve All Subjects
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveAllSubjects ()
    {
        try
        {
            $subjects = Subject::where('is_active', true)
                ->where('public_id', '!=', 'default')->get();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                MessageCode::msgSuccess('all subjects retrieved'),
                $subjects
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
