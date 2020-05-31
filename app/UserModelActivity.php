<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Log as DBLog;

class UserModelActivity extends Model
{
    protected $table = 'user_model_activities';

    protected $fillable = [
        'user__id',
        'action',
        'model_class',
        'model_id'
    ];

    protected $hidden = [
        'id'
    ];

    /**
     * Action Types
     */
    const ACTION_ASK = 'ask';
    const ACTION_ANSWER = 'answer';
    const ACTION_COMMENT = 'comment';
    const ACTION_VIEW = 'view';
    const ACTION_UPDATE = 'update';
    const ACTION_UPVOTE = 'upvote';
    const ACTION_DOWNVOTE = 'downvote';
    const ACTION_FAVORITE = 'favorite';

    /**
     * Relationship One-to-Many with User
     * Get user that owns this model-activity
     */
    public function user()
    {
        return $this->belongsTo('App/User', 'user__id');
    }

    /**
     * Helpers
     */
    public function recordUserModelActivity ($userId, $action, $modelClass, $modelId)
    {
        try {
            if(!isset($userId) || !isset($action) || !isset($modelClass) || !isset($modelId)) {
                throw new \UnexpectedValueException('Insufficient information for user model activity');
            }

            if($action === self::ACTION_VIEW) {
                $exist = self::where('user__id', $userId)
                    ->where('action', $action)
                    ->where('model_class', $modelClass)
                    ->where('model_id', $modelId)
                    ->first();

                if(isset($exist)) {
                    throw new \UnexpectedValueException('Duplicated user model activity');
                }
            }

            self::create([
                'user__id' => $userId,
                'action' => $action,
                'model_class' => $modelClass,
                'model_id' => $modelId
            ]);

            return true;
        }
        catch(\Exception $exception) {
            DBLog::error($exception);
            return false;
        }
    }
}
