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
}
