<?php

namespace Spiral\Pages\Conditions;

use Spiral\Pages\PageConditionInterface;

class EnLocaleOnly implements PageConditionInterface
{
    public function hasMatches($context): bool
    {
        //return if ru locale is set
    }
}