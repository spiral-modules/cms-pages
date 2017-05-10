<?php

namespace Spiral\Pages\Services;

use Spiral\Core\Service;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Database\Sources\RevisionSource;

class RevisionsService extends Service
{
    /** @var RevisionSource */
    private $source;

    /** @var UniquePageContents */
    private $contents;

    /**
     * PageService constructor.
     *
     * @param RevisionSource     $source
     * @param UniquePageContents $contents
     */
    public function __construct(RevisionSource $source, UniquePageContents $contents)
    {
        $this->source = $source;
        $this->contents = $contents;
    }

    /**
     * Create revision based on page changes.
     *
     * @param Page $page
     * @param Page $prev
     * @return Revision
     */
    public function makeRevision(Page $page, Page $prev): Revision
    {
        $revision = $this->source->createFromPage(
            $prev,
            $this->timeStarted($prev),
            new \DateTime()
        );

        $revision->editor = $prev->editor;
        $revision->diff = $this->contents->calcDiff($page, $prev);

        return $revision;
    }

    /**
     * @param Page $prev
     * @return \DateTimeInterface
     */
    protected function timeStarted(Page $prev): \DateTimeInterface
    {
        $last = $this->source->findLastForPage($prev);
        if (!empty($last)) {
            $timeStarted = $last->time_ended;
        } else {
            $timeStarted = $prev->time_created;
        }

        return $timeStarted;
    }
}