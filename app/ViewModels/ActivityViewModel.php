<?php


namespace App\ViewModels;


use App\Http\Support\Supporter;
use App\Log;
use App\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActivityViewModel
{
    protected $supporter;

    public function __construct()
    {
        $this->supporter = new Supporter();
    }

    public function doGetMyPostsSQL ($authorId)
    {
        try
        {
            //** SQL get questions that the author has asked */
            $myQuestionsSql = DB::table('questions AS q')
                ->select(
                    DB::raw('\'Q\' AS "type"'),
                    'q.public_id AS question_public_id',
                    DB::raw('null AS "answer_public_id"'),
                    'q.title',
                    'q.posted_at',
                    'q.updated_at',
                    'q.subject__id',
                    'q.id')
                ->where('q.user__id', '=', $authorId)
                ->where('q.is_deleted', '=', false)
                ->where('q.is_draft', '=', false)
                ->whereNotNull('q.posted_at');

            //** SQL get questions that the author has answered */
            $questionsAnsweredSql = DB::table('questions AS q1')
                ->select(
                    DB::raw('\'A\' AS "type"'),
                    'q1.public_id AS question_public_id',
                    'a.public_id AS answer_public_id',
                    'q1.title',
                    'q1.posted_at',
                    'q1.updated_at',
                    'q1.subject__id',
                    'q1.id')
                ->join('answers AS a', 'a.question__id', '=', 'q1.id')
                ->where('a.user__id', '=', $authorId)
                ->where('a.is_deleted', '=', false)
                ->where('a.is_draft', '=', false)
                ->whereNotNull('a.posted_at')
                ->where('q1.is_deleted', '=', false)
                ->where('q1.is_draft', '=', false)
                ->whereNotNull('q1.posted_at');

            //** SQL union above two collections of questions */
            $allQuestionsSql = $myQuestionsSql->union($questionsAnsweredSql);

            //** SQL sort union queries. It is not an elegant way in Laravel, but it's what I can do now. */
            $querySql = $allQuestionsSql->toSql();
            $allQuestionsSql = DB::table(DB::raw("($querySql order by posted_at asc) as a"))->mergeBindings($allQuestionsSql);

            return $allQuestionsSql;
        }
        catch(\Exception $exception)
        {
            Log::error($exception);
            return null;
        }
    }

    public function doManipulateDataMyPosts (Collection $questions)
    {
        try
        {
            foreach ($questions as $question) {
                $question->readable_time_en = $this->supporter->getHumanReadableActionDateAsString($question->posted_at, $question->updated_at, Supporter::ASK_ACTION);
                $question->readable_time_kh = $this->supporter->getHumanReadableActionDateAsString($question->posted_at, $question->updated_at, Supporter::ASK_ACTION);

                $question->subject = Subject::where('id', $question->subject__id)->where('is_active', true)->first();
                $question->tags = DB::table('tags AS t')
                    ->join('question_tag_mappings AS qtm', 'qtm.tag__id', '=', 't.id')
                    ->where('t.is_active', true)
                    ->where('qtm.question__id', '=', $question->id)
                    ->get();
            }

            return $questions;
        }
        catch(\Exception $exception)
        {
            Log::error($exception);
            return null;
        }
    }
}
