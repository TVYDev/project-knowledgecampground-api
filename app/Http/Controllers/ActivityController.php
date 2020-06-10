<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Http\Support\DatabaseSupporter;
use App\Http\Support\Supporter;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MessageCode;
use App\Libs\MiddlewareConst;
use App\Question;
use App\ViewModels\ActivityViewModel;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    use JsonResponse;

    const POST_TYPE_QUESTION = 'question';
    const POST_TYPE_ANSWER = 'answer';

    protected $supporter;
    protected $activityViewModel;
    protected $databaseSupporter;
    protected $inputsValidator;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH);
        $this->middleware(MiddlewareConst::JWT_CLAIMS);

        $this->supporter = new Supporter();
        $this->activityViewModel = new ActivityViewModel();
        $this->databaseSupporter = new DatabaseSupporter();
        $this->inputsValidator = new KCValidate();
    }

    /**
     * Retrieve My Posts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveMyPosts(Request $request)
    {
        try
        {
            $authorId = auth()->user()->id;
            $allQuestionsSql = $this->activityViewModel->doGetMyPostsSQL($authorId);

            $allQuestions = $this->databaseSupporter->getPaginatedDBData($allQuestionsSql, $request);

            $manipulatedQuestions = $this->activityViewModel->doManipulateDataMyPosts($allQuestions);

            $dataResponse = $this->supporter->getArrayResponseListPagination($manipulatedQuestions, $request);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                MessageCode::msgSuccess('my posts retrieved'),
                $dataResponse
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Vote a Post
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postVotePost (Request $request)
    {
        try {
            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_VOTE_POST);

            $postType = $request->post_type;
            $postPublicId = $request->post_public_id;
            $vote = $request->vote;

            $post = null;
            if ($postType === self::POST_TYPE_QUESTION) {
                $post = Question::where('public_id', $postPublicId)
                    ->where('is_draft', false)
                    ->where('is_active', true)
                    ->where('is_deleted', false)
                    ->first();
            }
            elseif ($postType === self::POST_TYPE_ANSWER) {
                $post = Answer::where('public_id', $postPublicId)
                    ->where('is_draft', false)
                    ->where('is_active', true)
                    ->where('is_deleted', false)
                    ->first();
            }

            if(isset($post)) {
                $userId = auth()->user()->id;

                if($post->userVotes()->wherePivot('user__id', $userId)->exists()) {
                    $post->userVotes()->updateExistingPivot($userId, [
                        'vote' => $vote,
                        'updated_by' => $userId
                    ]);
                }
                else {
                    $post->userVotes()->attach($userId, [
                        'vote' => $vote,
                        'created_by' => $userId
                    ]);
                }

                $numVotes = intval($post->userVotes()->sum('vote'));

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('post voted'),
                    ['vote' => $numVotes]
                );
            }
            else {
                return $this->standardJsonResponse(
                    HttpStatusCode::ERROR_BAD_REQUEST,
                    false,
                    MessageCode::msgError('post not found'),
                    null,
                    ErrorCode::ERR_CODE_DATA_NOT_EXIST
                );
            }
        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Manage Favorite Question
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postManageFavoriteQuestion (Request $request)
    {
        try
        {
            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_MANAGE_FAVORITE_QUESTION);

            $questionPublicId = $request->question_public_id;
            $isFavorite = $request->is_favorite;

            $question = Question::where('public_id', $questionPublicId)
                ->where('is_active', true)
                ->where('is_draft', false)
                ->where('is_deleted', false)
                ->first();
            if(isset($question)) {
                $userId = auth()->user()->id;

                if($isFavorite) {
                    $isAlreadyAttached = $question->userFavorites()->wherePivot('user__id', $userId)->exists();
                    if(!$isAlreadyAttached) {
                        $question->userFavorites()->attach($userId, [
                            'created_by' => $userId
                        ]);
                    }
                    $messageCode = MessageCode::msgSuccess('favorite question marked');
                }
                else {
                    $question->userFavorites()->detach($userId);
                    $messageCode = MessageCode::msgSuccess('favorite question unmarked');
                }
                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    $messageCode
                );
            }
            else {
                return $this->standardJsonResponse(
                    HttpStatusCode::ERROR_BAD_REQUEST,
                    false,
                    MessageCode::msgError('question not found'),
                    null,
                    ErrorCode::ERR_CODE_DATA_NOT_EXIST
                );
            }
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function postChooseBestAnswer (Request $request) {
        try {
            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_CHOOSE_BEST_ANSWER);

            $questionPublicId = $request->question_public_id;
            $answerPublicId = $request->answer_public_id;

            $question = Question::where('public_id', $questionPublicId)
                ->where('is_draft', false)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->first();

            if(isset($question)) {
                $answer = null;
                if(isset($answerPublicId)) {
                    $answer = Answer::where('public_id', $answerPublicId)
                        ->where('is_draft', false)
                        ->where('is_active', true)
                        ->where('is_deleted', false)
                        ->first();

                    if(isset($answer)) {
                        $question->best_answer__id = $answer->id;
                    }
                    else {
                        return $this->standardJsonResponse(
                            HttpStatusCode::ERROR_BAD_REQUEST,
                            false,
                            MessageCode::msgError('answer not found'),
                            null,
                            ErrorCode::ERR_CODE_DATA_NOT_EXIST
                        );
                    }
                }
                else {
                    $question->best_answer__id = null;
                }

                if(isset($question->best_answer_created_at)) {
                    $question->best_answer_updated_at = new \DateTime();
                }else {
                    $question->best_answer_created_at = new \DateTime();
                }

                $question->save();

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('best answer chosen')
                );
            }
            else {
                return $this->standardJsonResponse(
                    HttpStatusCode::ERROR_BAD_REQUEST,
                    false,
                    MessageCode::msgError('question not found'),
                    null,
                    ErrorCode::ERR_CODE_DATA_NOT_EXIST
                );
            }

        }
        catch(\Exception $exception) {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
