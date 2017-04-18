<?php

namespace Spiral\Pages\Services;

use Spiral\Pages\Database\Entities\AbstractPageEntity;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Config;
use Spiral\Pages\Utils;

class UniquePageContents
{
    /** @var HashService */
    private $hashes;

    /** @var Config */
    private $config;

    /** @var Utils */
    private $utils;

    /**
     * UniquePageContents constructor.
     *
     * @param HashService $hashes
     * @param Config      $config
     * @param Utils       $utils
     */
    public function __construct(HashService $hashes, Config $config, Utils $utils)
    {
        $this->hashes = $hashes;
        $this->config = $config;
        $this->utils = $utils;
    }

    /**
     * Are page contents are identical?
     *
     * @param Page $page
     * @param Page $prev
     * @return bool
     */
    public function contentsIdentical(Page $page, Page $prev): bool
    {
        return $this->hashes->compareHashes($prev->content_hash, $page->content_hash);
    }

    /**
     * Unique ID of page content.
     *
     * @param Page $page
     * @return string
     */
    public function contentID(Page $page): string
    {
        $data = $this->pickContent($page);

        return md5(serialize($data));
    }

    /**
     * Page content for unique comparison.
     *
     * @param AbstractPageEntity $page
     * @return array
     */
    protected function pickContent(AbstractPageEntity $page): array
    {
        return $this->utils->fetchKeys($page->getFields(), $this->config->fields());
    }

    /**
     * Page revisions diff line.
     *
     * @param Page               $page
     * @param AbstractPageEntity $prev
     * @return null|string
     */
    public function calcDiff(Page $page, AbstractPageEntity $prev)
    {
        $diff = array_diff_assoc($this->pickContent($page), $this->pickContent($prev));

        if (empty($diff)) {
            return null;
        }

        $fields = array_keys($diff);

        asort($fields);

        return join(', ', $fields);
    }
}