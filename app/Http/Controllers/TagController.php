<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\MessageCode;
use App\Subject;
use Illuminate\Http\Request;

class TagController extends Controller
{
    use JsonResponse;

    /**
     * Retrieve All Tags of Subject
     *
     * @param $subjectPublicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveAllTagsOfSubject ($subjectPublicId)
    {
        try
        {
            $subject = Subject::where('public_id', $subjectPublicId)
                ->where('is_active', true)
                ->first();

            if($subject){
                $tags = $subject->tags;

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('all tags retrieved'),
                    $tags
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('subject not exist')
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
