<?php

namespace Spiral\Pages;

/**
 * Interface PageConditionInterface
 *
 * @package Spiral\Pages
 */
interface PageConditionInterface
{
    /**
     * @param mixed $context
     * @return bool
     */
    public function hasMatches($context): bool;
}