<?php

namespace app\helpers;

use Coduo\PHPHumanizer\String\Humanize;

class HumanizerHelper
{
    public static function pluralize($count, $string)
    {
        return Humanize::pluralize($count, $string);
    }
}