<?php
/**
 * Created by PhpStorm.
 * User: vannyou.tan
 * Date: 26-Aug-19
 * Time: 8:17 PM
 */

namespace App\Http\Support;


class Supporter
{
    /***************************************
     * Action Constants
     ***************************************/
    const ASK_ACTION = 'asked';
    const ANSWER_ACTION = 'answered';
    const COMMENT_ACTION = 'commented';
    const REPLY_ACTION = 'replied';
    const DO_ACTION = 'did';

    public function getHumanReadableActionDateAsString ($stringPostedDate, $stringUpdatedDate = null, $typeOfAction = self::DO_ACTION)
    {
        $postedReadablePeriod = $this->getReadablePeriodToNow($stringPostedDate);
        $updatedReadablePeriod = null;

        $humanReadableActionDate = "$typeOfAction $postedReadablePeriod";

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
                        $readableTime = "$s second" . ($s == 1 ? '' : 's');
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
}
