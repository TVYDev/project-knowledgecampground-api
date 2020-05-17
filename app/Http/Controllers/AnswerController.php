<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Comment;
use App\Http\Support\Supporter;
use App\Libs\DirectoryStore;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MessageCode;
use App\Libs\MiddlewareConst;
use App\Question;
use App\User;
use App\UserAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    use JsonResponse;

    protected $support;
    protected $inputsValidator;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH, [
            'except' => [
                'getRetrieveListPostedAnswersOfQuestion',
                'getViewAnswer',
                'getRetrieveDescriptionOfAnswer'
            ]
        ]);

        $this->middleware(MiddlewareConst::JWT_CLAIMS, [
            'except' => [
                'getRetrieveListPostedAnswersOfQuestion',
                'getViewAnswer',
                'getRetrieveDescriptionOfAnswer'
            ]
        ]);

        $this->support = new Supporter();
        $this->inputsValidator = new KCValidate();
    }

    /**
     * Save Answer During Editing
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postSaveAnswerDuringEditing (Request $request)
    {
        try
        {
            DB::beginTransaction();

            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_ANSWER_SAVE_DURING_EDITING);

            $question = Question::where('public_id', $request->question_public_id)->first();

            $isDraft = ($request->is_draft === 'true' || $request->is_draft === true ) ? true : false;

            $answer = Answer::updateOrCreate(
                ['public_id' => $request->public_id],
                [
                    'user__id' => auth()->user()->id,
                    'question__id' => $question->id,
                    'is_draft' => $isDraft,
                ]
            );

            $answer->answerDescription()->updateOrCreate(
                ['answer__id' => $answer->id],
                ['tmp_data' => $request->description]
            );

            if($request->hasFile('image_file_upload') && $request->has('image_file_name'))
            {
                $request->image_file_upload->storeAs('public/answer_images', $request->image_file_name);
            }

            DB::commit();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                MessageCode::msgSuccess('answer saved'),
                $answer
            );
        }
        catch(\Exception $exception)
        {
            DB::rollback();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Save Answer
     *
     * @param Request $request
     * @param $publicId
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function putSaveAnswer (Request $request, $publicId)
    {
        try
        {
            DB::beginTransaction();

            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_ANSWER_SAVE);

            $isDraft = $request->is_draft;

            $answer = Answer::where('public_id', $publicId)->first();
            $originalIsDraft = $answer->is_draft;
            $answer->is_draft = $isDraft;
            if($originalIsDraft == true) {
                $answer->posted_at = new \DateTime();
            }
            else {
                $answer->updated_at = new \DateTime();
            }
            $answer->save();

            if($isDraft == false) {
                $answerDesc = $answer->answerDescription()->where('is_active', true)->first();
                $answer->answerDescription()->where('answer__id', $answer->id)->update([
                    'data' => $answerDesc->tmp_data,
                    'tmp_data' => null
                ]);
            }

            DB::commit();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                $isDraft ? MessageCode::msgSuccess('answer saved draft') : MessageCode::msgSuccess('answer saved'),
                $answer
            );
        }
        catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * View Answer
     *
     * @param $publicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViewAnswer ($publicId, Request $request)
    {
        try
        {
            $viewerPublicId = $request->has('viewer') ? $request->viewer : null;

            $answer = Answer::where('public_id', $publicId)
                        ->where('is_active', true)
                        ->where('is_deleted', false)
                        ->first();

            if($answer)
            {
                $answer['readable_time_en'] = $this->support->getHumanReadableActionDateAsString($answer->posted_at, $answer->updated_at, Supporter::ANSWER_ACTION);
                $answer['readable_time_kh'] = $this->support->getHumanReadableActionDateAsString($answer->posted_at, $answer->updated_at, Supporter::ANSWER_ACTION);
                $answer['author_name'] = $answer->user()->pluck('name')->first();
                $answer['author_id'] = $answer->user()->pluck('id')->first();

                $author = User::find($answer['author_id']);
                $answer['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($author);

                /* --- Get description of the answer --- */
                $description = $answer->answerDescription()->where('is_active', true)->first();
                $description['relative_path_store_images'] = $this->support->getFileUrl(null,DirectoryStore::RELATIVE_PATH_STORE_ANSWER_IMAGE);
                $answer['description'] = $description;

                /* --- Get comments of the question --- */
                $answer['comments'] = Comment::getCommentsOfCommentable(Comment::COMMENTABLE_ANSWER, $publicId);

                /* --- Get number of votes of the question --- */
                $viewer = User::where('public_id', $viewerPublicId)->first();
                $voteByViewer = 0;
                if(isset($viewer)) {
                    $selectVoteByViewer = $answer->userVotes()->wherePivot('user__id', $viewer->id)->pluck('vote')->first();
                    if(isset($selectVoteByViewer)) {
                        $voteByViewer = $selectVoteByViewer;
                    }
                }
                $answer['vote_by_viewer'] = $voteByViewer;
                $answer['vote'] = intval($answer->userVotes()->sum('vote'));

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('answer viewed'),
                    $answer
                );
            }
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('answer not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch (\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Retrieve Description of Answer
     *
     * @param $publicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveDescriptionOfAnswer ($publicId)
    {
        try
        {
            $answer = Answer::where('public_id', $publicId)
                        ->where('is_active', true)
                        ->where('is_deleted', false)
                        ->first();

            if($answer)
            {
                $answerDescription = $answer->answerDescription()->where('is_active', true)->first();
                $answerDescription['relative_path_store_images'] = $this->support->getFileUrl(null,DirectoryStore::RELATIVE_PATH_STORE_ANSWER_IMAGE);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('description answer retrieved'),
                    $answerDescription
                );
            }
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('answer not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Retrieve List Posted Answers of Question
     *
     * @param $questionPublicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveListPostedAnswersOfQuestion ($questionPublicId)
    {
        try
        {
            $question = Question::where('public_id', $questionPublicId)
                            ->where('is_draft', false)
                            ->where('is_active', true)
                            ->where('is_deleted', false)
                            ->first();

            if($question) {
                $answers = Answer::where('question__id', $question->id)
                    ->where('is_draft', false)
                    ->where('is_active', true)
                    ->where('is_deleted', false)
                    ->get();

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('list posted answers retrieved'),
                    $answers
                );
            }
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                MessageCode::msgError('question not exist'),
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
