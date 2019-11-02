<?php

namespace App\Http\Controllers;

use App\Http\Support\Supporter;
use App\Libs\DirectoryStore;
use App\Libs\ErrorCode;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Question;
use App\Subject;
use App\Tag;
use App\User;
use App\UserAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    use JsonResponse;

    protected $support;

    public function __construct()
    {
        $this->support = new Supporter();
    }

    public function postSaveDuringEditing (Request $request)
    {
        try
        {
            DB::beginTransaction();

            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(),KCValidate::VALIDATION_QUESTION_SAVE_DURING_EDITING);
            if($result !== true) return $result;

            $subject = Subject::where('public_id', 'default')->first();

            $question = Question::updateOrCreate(
                ['public_id' => $request->public_id],
                [
                    'title' => $request->title,
                    'user__id' => auth()->user()->id,
                    'subject__id' => $subject->id,
                    'is_draft' => $request->is_draft,
                    'posted_at' => new \DateTime()
                ]
            );

            $question->questionDescription()->updateOrCreate(
                ['question__id' => $question->id],
                ['data' => $request->description]
            );

            if($request->hasFile('image_file_upload') && $request->has('image_file_name'))
            {
                $request->image_file_upload->storeAs('public/question_images', $request->image_file_name);
            }

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
            DB::beginTransaction();

            // -- validate inputs
            $result = (new KCValidate())->doValidate($request->all(), KCValidate::VALIDATION_QUESTION_SAVE);
            if($result !== true) return $result;

            $isDraft = $request->is_draft;
            $tagPublicIds = $request->tag_public_ids;

            $subject = Subject::where('public_id', $request->subject_public_id)->first();

            $question = Question::where('public_id', $publicId)->first();
            $question->title = $request->title;
            $question->is_draft = $isDraft;
            $question->posted_at = new \DateTime();
            if(isset($subject)){
                $question->subject__id = $subject->id;
            }
            if(isset($tagPublicIds)){
                $tags = $subject->tags->whereIn('public_id', $tagPublicIds);
                foreach($tags as $t){
                    $question->tags()->attach($t->id);
                }
            }
            $question->save();

            DB::commit();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                $isDraft ? 'KC_MSG_SUCCESS__QUESTION_SAVE_DRAFT' : 'KC_MSG_SUCCESS__QUESTION_SAVE',
                $question
            );
        }
        catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function getQuestion($publicId)
    {
        try
        {
            $question = Question::where('public_id', $publicId)
                        ->where('is_active', true)
                        ->where('is_draft', false)
                        ->where('is_deleted', false)
                        ->first();

            if($question)
            {
                $question['readable_time_en'] = $this->support->getHumanReadableActionDateAsString($question->posted_at, $question->updated_at, Supporter::ASK_ACTION);
                $question['readable_time_kh'] = $this->support->getHumanReadableActionDateAsString($question->posted_at, $question->updated_at, Supporter::ASK_ACTION);
                $question['author_name'] = $question->user()->pluck('name')->first();
                $question['author_id'] = $question->user()->pluck('id')->first();

                $author = User::find($question['author_id']);
                $question['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($author);

                $question['relative_path_store_images_of_question'] = DirectoryStore::RELATIVE_PATH_STORE_QUESTION_IMAGE;

                $question['subject'] = $question->subject()->where('is_active', true)->first();
                $question['tags'] = $question->tags()->where('tags.is_active', true)->get();

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    null,
                    $question
                );
            }

            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                'KC_MSG_ERROR__QUESTION_NOT_EXIST',
                null,
                ErrorCode::ERR_CODE_DATA_NOT_EXIST
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function getDescriptionOfQuestion ($publicId)
    {
        try
        {
            $question = Question::where('public_id', $publicId)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->first();

            if($question)
            {
                $questionDescription = $question->questionDescription()->where('is_active', true)->first();
                $questionDescription['relative_path_store_images'] = DirectoryStore::RELATIVE_PATH_STORE_QUESTION_IMAGE;
                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    null,
                    $questionDescription
                );
            }
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                'KC_MSG_ERROR__QUESTION_NOT_EXIST',
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
