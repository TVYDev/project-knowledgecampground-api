<?php


namespace App\ViewModels;


use App\Http\Support\Supporter;
use App\Question;
use App\Log as DBLog;
use App\UserModelActivity;

class QuestionViewModel
{
    protected $supporter;

    public function __construct()
    {
        $this->supporter = new Supporter();
    }

    public function getSummaryInfo ($questionId)
    {
        try {
            $question = Question::find($questionId);
            if(!isset($question)) {
                throw new \UnexpectedValueException('Unable to get summary info, question not found');
            }

            $numViews = UserModelActivity::where('model_id', $questionId)
                            ->where('model_class', Question::class)
                            ->where('action', UserModelActivity::ACTION_VIEW)
                            ->count();

            $lastActiveUserModelActivity = UserModelActivity::where('model_id', $questionId)
                                                ->where('model_class', Question::class)
                                                ->where('action', '!=', UserModelActivity::ACTION_VIEW)
                                                ->latest()
                                                ->first();

            $lastActiveDate = $lastActiveUserModelActivity->created_at ?? null;
            $lastActiveDate = $this->supporter->getHumanReadableActionDateAsString($lastActiveDate);

            return [
                'num_views' => $numViews ?? 'N/A',
                'last_active_date' => $lastActiveDate ?? 'N/A'
            ];
        }
        catch(\Exception $exception) {
            DBLog::error($exception);
            return null;
        }
    }
}
