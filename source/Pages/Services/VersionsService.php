<?php

namespace Spiral\Pages\Services;

use Spiral\Pages\Database\Page;

/**
 * Class ConditionsMatcher
 *
 * @package Spiral\Pages
 */
class VersionsService
{
    /**
     * @param Page  $page
     * @param mixed $context
     * @return Page
     */
    public function findVersion(Page $page, $context): Page
    {
        foreach ($page->versions() as $version) {
            if ($this->versionMatches($version, $context)) {
                return $version;
            }
        }

        return $page;
    }

    /**
     * @param Page  $version
     * @param mixed $context
     * @return bool
     */
    protected function versionMatches(Page $version, $context): bool
    {
        foreach ($version->conditions() as $condition) {
            if (!$condition->hasMatches($context)) {
                return false;
            }
        }

        return true;
    }
}