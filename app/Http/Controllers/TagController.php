<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Subject;
use Illuminate\Http\Request;

class TagController extends Controller
{
    use JsonResponse;

    public function getAllTagsOfSubject ($subjectPublicId)
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
                    null,
                    $tags
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                'KC_MSG_ERROR__SUBJECT_NOT_EXIST'
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
