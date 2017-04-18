<?php

namespace Spiral\Pages\Conditions;

use Spiral\Pages\PageConditionInterface;

class GuestOnly implements PageConditionInterface
{
    public function hasMatches($context): bool
    {
        //return if user is not authorized
    }
}