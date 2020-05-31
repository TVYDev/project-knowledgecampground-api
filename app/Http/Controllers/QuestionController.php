<?php

namespace App\Http\Controllers;

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
use App\Subject;
use App\User;
use App\UserAvatar;
use App\UserModelActivity;
use App\ViewModels\QuestionViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    use JsonResponse;

    protected $support;
    protected $inputsValidator;
    protected $userModelActivity;
    protected $questionViewModel;

    public function __construct()
    {
        $this->middleware(MiddlewareConst::JWT_AUTH, [
            'except' => [
                'getRetrieveListOfQuestions',
                'getRetrieveSubjectTagsOfQuestion',
                'getViewQuestion',
                'getRetrieveDescriptionOfQuestion'
            ]
        ]);

        $this->middleware(MiddlewareConst::JWT_CLAIMS, [
            'except' => [
                'getRetrieveListOfQuestions',
                'getRetrieveSubjectTagsOfQuestion',
                'getViewQuestion',
                'getRetrieveDescriptionOfQuestion'
            ]
        ]);

        $this->support = new Supporter();
        $this->inputsValidator = new KCValidate();
        $this->userModelActivity = new UserModelActivity();
        $this->questionViewModel = new QuestionViewModel();
    }

    /**
     * Save Question During Editing
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function postSaveQuestionDuringEditing (Request $request)
    {
        try
        {
            DB::beginTransaction();

            /* --- validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_QUESTION_SAVE_DURING_EDITING);

            $isDraft = ($request->is_draft === 'true' || $request->is_draft === true ) ? true : false;

            $dataToUpdate = [
                'title' => $request->title,
                'user__id' => auth()->user()->id,
                'is_draft' => $isDraft
            ];

            if($isDraft === true) {
                $subject = Subject::where('public_id', 'default')->first();
                $dataToUpdate['subject__id'] = $subject->id;
            }

            $question = Question::updateOrCreate(
                ['public_id' => $request->public_id],
                $dataToUpdate
            );

            $question->questionDescription()->updateOrCreate(
                ['question__id' => $question->id],
                ['tmp_data' => $request->description]
            );

            if($request->hasFile('image_file_upload') && $request->has('image_file_name'))
            {
                $request->image_file_upload->storeAs('public/question_images', $request->image_file_name);
            }

            DB::commit();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_CREATED,
                true,
                MessageCode::msgSuccess('question saved'),
                $question
            );
        }
        catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Save Question
     *
     * @param Request $request
     * @param $publicId
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function putSaveQuestion (Request $request, $publicId)
    {
        try
        {
            DB::beginTransaction();

            /* --- validate inputs --- */
            $this->inputsValidator->doValidate($request->all(), KCValidate::VALIDATION_QUESTION_SAVE);

            $isDraft = $request->is_draft;
            $tagPublicIds = $request->tag_public_ids;

            $subject = Subject::where('public_id', $request->subject_public_id)->first();

            $question = Question::where('public_id', $publicId)->first();
            $originalIsDraft = $question->is_draft;
            $question->title = $request->title;
            $question->is_draft = $isDraft;
            if($originalIsDraft == true) {
                $question->posted_at = new \DateTime();
            }
            else {
                $question->updated_at = new \DateTime();
            }
            if(isset($subject)){
                $question->subject__id = $subject->id;
            }
            if(isset($tagPublicIds)){
                $question->tags()->detach();
                $tags = $subject->tags->whereIn('public_id', $tagPublicIds);
                foreach($tags as $t){
                    $question->tags()->attach($t->id);
                }
            }

            $question->save();

            if($isDraft == false) {
                $questionDesc = $question->questionDescription()->where('is_active', true)->first();
                $question->questionDescription()->where('question__id', $question->id)->update([
                    'data' => $questionDesc->tmp_data,
                    'tmp_data' => null
                ]);
            }

            DB::commit();

            /* --- Record user model activity --- */
            $this->userModelActivity->recordUserModelActivity(
                auth()->user()->id,
                UserModelActivity::ACTION_ASK,
                Question::class,
                $question->id
            );

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                $isDraft ? MessageCode::msgSuccess('question saved draft') : MessageCode::msgSuccess('question saved'),
                $question
            );
        }
        catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * View Question
     *
     * @param $publicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViewQuestion($publicId, Request $request)
    {
        try
        {
            $viewerPublicId = $request->has('viewer') ? $request->viewer : 'N/A';

            $question = Question::where('public_id', $publicId)
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

                /* --- Get description of the question --- */
                $description = $question->questionDescription()->where('is_active', true)->first();
                $description['relative_path_store_images'] = $this->support->getFileUrl(null,DirectoryStore::RELATIVE_PATH_STORE_QUESTION_IMAGE);
                $question['description'] = $description;

                /* --- Get comments of the question --- */
                $question['comments'] = Comment::getCommentsOfCommentable(Comment::COMMENTABLE_QUESTION, $publicId);

                /* --- Get number of votes of the question --- */
                $viewer = User::where('public_id', $viewerPublicId)->first();
                $voteByViewer = 0;
                $isFavoriteByViewer = false;
                if(isset($viewer)) {
                    $selectVoteByViewer = $question->userVotes()->wherePivot('user__id', $viewer->id)->pluck('vote')->first();
                    if(isset($selectVoteByViewer)) {
                        $voteByViewer = $selectVoteByViewer;
                    }
                    $isFavoriteByViewer = $question->userFavorites()->wherePivot('user__id', $viewer->id)->exists();

                    /* --- Record user model activity --- */
                    $this->userModelActivity->recordUserModelActivity($viewer->id, UserModelActivity::ACTION_VIEW, Question::class, $question->id);
                }
                $question['vote_by_viewer'] = $voteByViewer;
                $question['vote'] = intval($question->userVotes()->sum('vote'));
                $question['is_favorite_by_viewer'] = $isFavoriteByViewer;
                $question['summary_info'] = $this->questionViewModel->getSummaryInfo($question->id);

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('question viewed'),
                    $question
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

    /**
     * Retrieve Subject and Tags of Question
     *
     * @param $publicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveSubjectTagsOfQuestion ($publicId)
    {
        try
        {
            $question = Question::where('public_id', $publicId)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->first();

            if(isset($question))
            {
                $question['subject'] = $question->subject()->where('is_active', true)->first();
                $question['tags'] = $question->tags()->where('tags.is_active', true)->get();

                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('subject tags retrieved'),
                    $question
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

    /**
     * Retrieve Description of Question
     *
     * @param $publicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveDescriptionOfQuestion ($publicId)
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
                $questionDescription['relative_path_store_images'] = $this->support->getFileUrl(null,DirectoryStore::RELATIVE_PATH_STORE_QUESTION_IMAGE);
                return $this->standardJsonResponse(
                    HttpStatusCode::SUCCESS_OK,
                    true,
                    MessageCode::msgSuccess('description question retrieved'),
                    $questionDescription
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

    /**
     * Retrieve List of Questions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRetrieveListOfQuestions (Request $request)
    {
        try
        {
            $questionsSql = Question::where('is_active', true)
                            ->where('is_draft', false)
                            ->where('is_deleted', false);

            if($request->has('search')) {
                $keywords = explode(' ', $request->search);
                $questionsSql->where(function($q) use($keywords){
                    foreach ($keywords as $keyword) {
                        if(!empty($keyword)) {
                            $q->orWhere('title', 'ilike', '%'.trim($keyword).'%');
                        }
                    }
                });
            }

            $questionsSql->orderBy('posted_at', 'desc');

            $perPage = 10;
            $page = 1;
            if($request->has('page')) {
                $perPage = $request->per_page;
                $page = $request->page;

                $offset = $perPage * ($page - 1);
                $questionsSql->offset($offset)->limit($perPage);
            }

            $questions = $questionsSql->select(DB::raw('*, COUNT(*) OVER() AS total'))->get();
            foreach ($questions as $question) {
                $question['readable_time_en'] = $this->support->getHumanReadableActionDateAsString($question->posted_at, $question->updated_at, Supporter::ASK_ACTION);
                $question['readable_time_kh'] = $this->support->getHumanReadableActionDateAsString($question->posted_at, $question->updated_at, Supporter::ASK_ACTION);

                $question['subject'] = $question->subject()->where('is_active', true)->first();
                $question['tags'] = $question->tags()->where('tags.is_active', true)->get();

                $author['name'] = $question->user()->pluck('name')->first();
                $author['id'] = $question->user()->pluck('id')->first();
                $user = User::find($author['id']);
                $author['avatar_url'] = $this->support->getFileUrl((new UserAvatar())->getActiveUserAvatarUrl($user));
                $question['author'] = $author;
                $question['vote'] = intval($question->userVotes()->sum('vote'));
            }

            $total = 0;
            if(!empty($questions) && count($questions) > 0) {
                $total = intval($questions[0]->total);
            }

            $dataResponse = $this->support->getArrayResponseListPagination($questions, $request);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                MessageCode::msgSuccess('list question retrieved'),
                $dataResponse
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
