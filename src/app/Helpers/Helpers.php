<?php

if (!function_exists('telegram')) {
    /**
     * @return \Telegram\Bot\Api
     */
    function telegram()
    {
        return app('telegram');
    }
}
