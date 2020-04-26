<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Support\Supporter;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MessageCode;
use App\Libs\MiddlewareConst;
use App\Reply;
use App\User;
use App\UserAvatar;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    use JsonResponse;

    protected $supporter;
    protected $inputsValidator;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH);
        $this->middleware(MiddlewareConst::JWT_CLAIMS);

        $this->supporter = new Supporter();
        $this->inputsValidator = new KCValidate();
    }

    /**
     * Save Reply
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postSaveReply (Request $request)
    {
        try
        {
            /* --- Validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_REPLY_SAVE);

            $commentPublicId = $request->comment_public_id;
            $body = $request->body;

            $comment = Comment::where('public_id', $commentPublicId)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->first();

            if($comment)
            {
                $newReplyPublicId = $this->supporter->generatePublicId();
                $reply = new Reply([
                    'public_id' => $newReplyPublicId,
                    'body' => $body,
                    'user__id' => auth()->user()->id
                ]);
                $comment->replies()->save($reply);

                $reply['readable_time_en'] = $this->supporter->getHumanReadableActionDateAsString($reply->created_at, $comment->updated_at);
                $reply['readable_time_kh'] = $this->supporter->getHumanReadableActionDateAsString($reply->created_at, $comment->updated_at);

                $reply['author_name'] = $reply->user()->pluck('name')->first();
                $reply['author_id'] = $reply->user()->pluck('id')->first();

                $author = User::find($reply['author_id']);
                $reply['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($author);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_CREATED,
                    true,
                    MessageCode::msgSuccess('reply saved'),
                    $reply
                );
            }
            else
            {
                return $this->standardJsonResponse(
                    HttpStatusCode::ERROR_BAD_REQUEST,
                    false,
                    MessageCode::msgError('comment not exist'),
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
