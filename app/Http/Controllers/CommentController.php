<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Comment;
use App\Http\Support\Supporter;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Question;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use JsonResponse;

    protected $supporter;

    public function __construct()
    {
        $this->supporter = new Supporter();
    }

    public function postSave (Request $request)
    {
        try
        {
            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_COMMENT_SAVE);
            if($result !== true)    return $result;

            $commentableType = $request->commentable_type;
            $commentablePublicId = $request->commentable_public_id;
            $body = $request->body;

            $commentable = null;
            if($commentableType == 'question')
            {
                $commentable = Question::where('public_id', $commentablePublicId)->first();
            }
            else
            {
                $commentable = Answer::where('public_id', $commentablePublicId)->first();
            }

            if(isset($commentable))
            {
                $newCommentPublicId = $this->supporter->generatePublicId();
                $comment = new Comment([
                    'public_id' => $newCommentPublicId,
                    'body'      => $body,
                    'user__id'  => auth()->user()->id
                ]);
                $commentable->comments()->save($comment);
            }
            else
            {
                return $this->standardJsonResponse(
                    HttpStatusCode::ERROR_BAD_REQUEST,
                    false,
                    'KC_MSG_ERROR__COMMENTABLE_MODEL_NOT_EXIST',
                    null,
                    ErrorCode::ERR_CODE_DATA_NOT_EXIST
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                'KC_MSG_SUCCESS__COMMENT_SAVE',
                $comment
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
