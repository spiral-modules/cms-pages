<?php

namespace Spiral\Pages\Conditions;

use Spiral\Pages\PageConditionInterface;

class DefaultLocaleOnly implements PageConditionInterface
{
    public function hasMatches($context): bool
    {
        //return if default locale is set
    }
}