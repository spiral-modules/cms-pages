<?php

namespace Spiral\Pages;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Sources\PageSource;

/**
 * Class FinderService
 *
 * @package Spiral\Pages\Services
 */
class Pages
{
    /** @var PageSource */
    protected $source;

    /**
     * FinderService constructor.
     *
     * @param PageSource      $source
     */
    public function __construct(PageSource $source)
    {
        $this->source = $source;
    }

    /**
     * @param string $uri
     * @return null|Page
     */
    public function find(string $uri)
    {
        return $this->source->findBySlug($uri);
    }
}