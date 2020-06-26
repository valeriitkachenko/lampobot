<?php

namespace App\Traits\Commands;

trait Regexable
{
    /**
     * @return string
     */
    public function getRegexPattern()
    {
        return $this->regexPattern;
    }
}
