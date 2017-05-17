<?php

namespace Spiral\Pages;

use Spiral\Core\Service;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Sources\PageSource;

/**
 * Class FinderService
 *
 * @package Spiral\Pages\Services
 */
class Pages extends Service
{
    /** @var PageSource */
    private $source;

    /**
     * Pages constructor.
     *
     * @param PageSource $source
     */
    public function __construct(PageSource $source)
    {
        $this->source = $source;
    }

    /**
     * @param string $uri
     * @param bool   $activeOnly
     * @return null|Page
     */
    public function find(string $uri, bool $activeOnly = true)
    {
        return $this->source->findBySlug($uri, $activeOnly);
    }

    /**
     * @param Page  $page
     * @param array $defaults
     * @return array
     */
    public function getMeta(Page $page, array $defaults): array
    {
        return [
            'title'       => $page->title,
            'keywords'    => $page->keywords ?: $this->defaults($defaults, 'keywords'),
            'description' => $page->description ?: $this->defaults($defaults, 'description'),
            'metaTags'    => $page->metaTags ?: $this->defaults($defaults, 'metaTags')
        ];
    }

    /**
     * @param array  $defaults
     * @param string $field
     * @param string $default
     * @return string
     */
    protected function defaults(array $defaults, string $field, string $default = ''): string
    {
        return isset($defaults[$field]) ? $defaults[$field] : $default;
    }
}