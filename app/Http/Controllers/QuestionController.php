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

    public function postSaveDuringEditing (Request $request)
    {
        try
        {
            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(),KCValidate::VALIDATION_QUESTION_SAVE_DURING_EDITING);
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

    public function putSave (Request $request, $publicId)
    {
        try
        {
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_QUESTION_SAVE);
            if($result !== true) return $result;

            $question = Question::where('public_id', $publicId)->first();
            $question->title = $request->title;
            $question->is_draft = $request->is_draft;
            $question->posted_at = new \DateTime();
            $question->save();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                'KC_MSG_SUCCESS__QUESTION_SAVE',
                $question
            );
        }
        catch(\Exception $exception)
        {
            return $exception->getMessage();
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
