<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Question;
use App\QuestionDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    use JsonResponse;

    public function postSave(Request $request)
    {
        try
        {
            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(),KCValidate::VALIDATION_QUESTION_SAVE);
            if($result !== true) return $result;

            DB::beginTransaction();

            $question = Question::updateOrCreate(
                ['public_id' => $request->public_id],
                [
                    'title' => $request->title,
                    'user__id' => auth()->user()->id,
                    'is_draft' => $request->is_draft,
                    'posted_at' => new \DateTime()
                ]
            );

            $question->questionDescription()->updateOrCreate(
                ['question__id' => $question->id],
                ['data' => $request->description]
            );

            DB::commit();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                'KC_MSG_SUCCESS__QUESTION_SAVE',
                $question
            );
        }
        catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
