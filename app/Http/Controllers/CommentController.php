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
use App\User;
use App\UserAvatar;
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
            else if($commentableType == 'answer')
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

                $comment['readable_time_en'] = $this->supporter->getHumanReadableActionDateAsString($comment->created_at, $comment->updated_at);
                $comment['readable_time_kh'] = $this->supporter->getHumanReadableActionDateAsString($comment->created_at, $comment->updated_at);

                $comment['author_name'] = $comment->user()->pluck('name')->first();
                $comment['author_id'] = $comment->user()->pluck('id')->first();

                $author = User::find($comment['author_id']);
                $comment['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($author);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_CREATED,
                    true,
                    'KC_MSG_SUCCESS__COMMENT_SAVE',
                    $comment
                );
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
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function getListPostedCommentsOfCommentableModel ($commentableType, $commentPublicId)
    {
        try
        {
            $commentable = null;
            if($commentableType == 'question')
            {
                $commentable = Question::where('public_id', $commentPublicId)->first();
            }
            elseif ($commentableType == 'answer')
            {
                $commentable = Answer::where('public_id', $commentPublicId)->first();
            }

            if(isset($commentable))
            {
                $comments = $commentable->comments;
                foreach ($comments as $comment)
                {
                    $comment['readable_time_en'] = $this->supporter->getHumanReadableActionDateAsString($comment->created_at, $comment->updated_at);
                    $comment['readable_time_kh'] = $this->supporter->getHumanReadableActionDateAsString($comment->created_at, $comment->updated_at);
                    $comment['author_name'] = $comment->user()->pluck('name')->first();
                    $comment['author_id'] = $comment->user()->pluck('id')->first();

                    $author = User::find($comment['author_id']);
                    $comment['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($author);
                }
                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    '',
                    $comments
                );
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
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
