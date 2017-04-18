<?php

namespace Spiral\Pages\Conditions;

use Spiral\Pages\PageConditionInterface;

class AuthorizedOnly implements PageConditionInterface
{
    public function hasMatches($context): bool
    {
        //return if user is authorized
    }
}