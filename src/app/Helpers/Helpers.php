<?php

if (!function_exists('seconds_to_hours')) {
    function seconds_to_hours(int $seconds): int
    {
        $secondsInOneHour = 3600;

        return $seconds / $secondsInOneHour;
    }
}
