<?php
/**
 * Created by PhpStorm.
 * User: vannyou.tan
 * Date: 26-Aug-19
 * Time: 8:17 PM
 */

namespace App\Http\Support;


use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class Supporter
{
    /***************************************
     * Action Constants
     ***************************************/
    const ASK_ACTION = 'asked';
    const ANSWER_ACTION = 'answered';
    const COMMENT_ACTION = 'commented';
    const REPLY_ACTION = 'replied';

    public function getHumanReadableActionDateAsString ($stringPostedDate, $stringUpdatedDate = null, $typeOfAction = null)
    {
        $postedReadablePeriod = $this->getReadablePeriodToNow($stringPostedDate);
        $updatedReadablePeriod = null;

        $typeOfAction = isset($typeOfAction) ? "$typeOfAction " : '';
        $humanReadableActionDate = $typeOfAction.$postedReadablePeriod;

        if(isset($stringUpdatedDate) && (new \DateTime($stringUpdatedDate) > new \DateTime($stringPostedDate))) {
            $updatedReadablePeriod = $this->getReadablePeriodToNow($stringUpdatedDate);
            $humanReadableActionDate .= " (edited $updatedReadablePeriod)";
        }

        return $humanReadableActionDate;
    }

    private function getReadablePeriodToNow (string $stringStartedDate)
    {
        $dateInterval = date_diff(new \DateTime($stringStartedDate), new \DateTime(), true);

        $d = $dateInterval->d;
        $h = $dateInterval->h;
        $i = $dateInterval->i;
        $s = $dateInterval->s;

        // condition --> less than a week (7 days)
        if($d < 7) {
            $readableTime = "$d day" . ($d == 1 ? '' : 's');

            // condition --> less than a day
            if($d == 0) {
                $readableTime = "$h hour" . ($h == 1 ? '' : 's');

                // condition --> less than an hour
                if($h == 0) {
                    $readableTime = "$i minute" . ($i == 1 ? '' : 's');

                    // condition --> less than a minute
                    if($i == 0) {
                        if($s >= 10) {
                            $readableTime = "$s seconds";
                        }
                        else {
                            $readableTime = 'few seconds';
                        }
                    }
                }
            }
            $readableTime .= ' ago';
        }
        // condition --> equal to 1 week
        elseif($d == 7) {
            $readableTime = '1 week ago';
        }
        // condition --> more than 1 week
        else {
            $readableTime = date('M d, Y \a\t H:i', strtotime($stringStartedDate));;
        }

        return $readableTime;
    }

    public function generatePublicId ()
    {
        $availableChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $randomString = '';
        $randomStringLength = 10;

        for ($i=0; $i<$randomStringLength; $i++){
            $randomIndex = rand(0, strlen($availableChars) - 1);
            $randomString .= $availableChars[$randomIndex];
        }

        return $randomString;
    }

    public function getFileUrl ($filename, $relativePath = null)
    {
        return url('/').$relativePath.$filename;
    }

    public function getArrayResponseListPagination ($data, Request $request)
    {
        try {
            $total = 0;
            if(!empty($data) && count($data) > 0) {
                $total = intval($data[0]->total);
            }

            $perPage = $request->has('per_page') ? $request->per_page : 10;
            $page = $request->has('page') ? $request->page : 1;

            return [
                'data' => $data,
                'pagination' => [
                    'total_records' => $total,
                    'num_records' => empty($data) ? 0 : count($data),
                    'per_page' => $perPage,
                    'page' => $page
                ]
            ];
        }
        catch(\Exception $exception)
        {
            Log::error($exception);
            return null;
        }
    }

    public function sendMailFromKC ($emailTo, $view, $data, $subject)
    {
        Mail::send($view, $data, function($message) use ($emailTo, $subject) {
            $message->to($emailTo)
                ->subject($subject);
            $message->from(env('MAIL_FROM_EMAIL'), env('MAIL_FROM_NAME'));
        });
    }

    public function sendEmailResetPassword ($emailTo, $resetLink)
    {
        try {
            $data = array('link' => $resetLink);
            self::sendMailFromKC($emailTo, 'emails.mail', $data, 'Reset Password');
            return true;
        }
        catch(\Exception $exception) {
            return false;
            // TODO: Add log
        }
    }

    public function sendEmailSuccessfulResetPassword ($emailTo)
    {
        try {
            self::sendMailFromKC($emailTo, 'emails.successful_reset_password', [], 'Successfully Reset Password');
            return true;
        }
        catch(\Exception $exception) {
            return false;
            // TODO: Add log
        }
    }

    public static function getBearerToken ($token)
    {
        try {
            if(isset($token)) {
                $t = explode(' ', $token);
                if(count($t) == 2) {
                    return $t[1];
                }
            }
        }
        catch(\Exception $exception) {
            // TODO: Add log
        }
        return null;
    }
}
