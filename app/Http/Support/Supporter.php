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
    public function getHumanReadableActionDateAsString ($stringPostedDate)
    {
        $postedDate = new \DateTime($stringPostedDate);
        $now = new \DateTime();
        $diff = date_diff($postedDate, $now, true);

        $d = $diff->d;
        $h = $diff->h;
        $i = $diff->i;
        $s = $diff->s;

        $readableTime = null;

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
            $readableTime = date('Y M d \a\t H:i', strtotime($stringPostedDate));
        }

        return $readableTime;
    }
}
