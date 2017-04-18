<?php

namespace Spiral\Pages;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Services\VersionsService;

/**
 * Class FinderService
 *
 * @package Spiral\Pages\Services
 */
class Pages
{
    /** @var PageSource */
    protected $source;

    /** @var VersionsService */
    protected $versions;

    /**
     * FinderService constructor.
     *
     * @param PageSource      $source
     * @param VersionsService $versions
     */
    public function __construct(PageSource $source, VersionsService $versions)
    {
        $this->source = $source;
        $this->versions = $versions;
    }

    /**
     * @param string     $uri
     * @param mixed|null $context
     * @return null|Page
     */
    public function find(string $uri, $context = null)
    {
        $page = $this->source->findBySlug($uri);
        if (empty($page)) {
            return null;
        }

//        if ($page->hasVersions()) {
//            $page = $this->versions->findVersion($page, $context);
//        }

        return $page;
    }
}