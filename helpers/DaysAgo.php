<?php

namespace Helper;

trait DaysAgo
{
    private function getDaysAgoText($unixDate)
    {
        return ($this->olderThanTwelveWeeks($unixDate)) ? date("l, j F Y", $unixDate) : $this->getTimeAgo($unixDate);
    }

    private function getTimeAgo($ptime)
    {
        $estimate_time = time() - $ptime;

        if ($estimate_time < 1) {
            return 'less than 1 second ago';
        }

        $condition = array(
                    12 * 30 * 24 * 60 * 60 => 'year',
                    30 * 24 * 60 * 60 => 'month',
                    24 * 60 * 60 => 'day',
                    60 * 60 => 'hour',
                    60 => 'minute',
                    1 => 'second',
        );

        foreach ($condition as $secs => $str) {
            $d = $estimate_time / $secs;

            if ($d >= 1) {
                $r = round($d);

                return $r.' '.$str.($r > 1 ? 's' : '').' ago';
            }
        }
    }

    private function olderThanTwelveWeeks($unixTime)
    {
        return ($unixTime <= time() - (60*60*24*7*12));
    }
}
