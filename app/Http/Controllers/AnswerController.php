<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    use JsonResponse;

    public function postSaveDuringEditing (Request $request)
    {
        try
        {
            DB::beginTransaction();

            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_ANSWER_SAVE_DURING_EDITING);
            if($result !== true) return $result;

            $question = Question::where('public_id', $request->question_public_id)->first();

            $answer = Answer::updateOrCreate(
                ['public_id' => $request->public_id],
                [
                    'user__id' => auth()->user()->id,
                    'question__id' => $question->id,
                    'is_draft' => $request->is_draft,
                    'posted_at' => new \DateTime()
                ]
            );

            $answer->answerDescription()->updateOrCreate(
                ['answer__id' => $answer->id],
                ['data' => $request->description]
            );

            if($request->hasFile('image_file_upload') && $request->has('image_file_name'))
            {
                $request->image_file_upload->storeAs('public/answer_images', $request->image_file_name);
            }

            DB::commit();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                'KC_MSG_SUCCESS__ANSWER_SAVE',
                $answer
            );
        }
        catch(\Exception $exception)
        {
            DB::rollback();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function putSave (Request $request, $publicId)
    {
        try
        {
            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_ANSWER_SAVE);
            if($result !== true) return $result;

            $isDraft = $request->is_draft;

            $answer = Answer::where('public_id', $publicId)->first();
            $answer->is_draft = $isDraft;
            $answer->posted_at = new \DateTime();
            $answer->save();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                $isDraft ? 'KC_MSG_SUCCESS__ANSWER_SAVE_DRAFT' : 'KC_MSG_SUCCESS__ANSWER_SAVE',
                $answer
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
