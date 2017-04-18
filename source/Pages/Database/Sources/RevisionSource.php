<?php

namespace Spiral\Pages\Database\Sources;

use Spiral\Database\Builders\SelectQuery;
use Spiral\ORM\Entities\RecordSelector;
use Spiral\ORM\Entities\RecordSource;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;

class RevisionSource extends RecordSource
{
    const RECORD = Revision::class;

    /**
     * @param Page $page
     * @return RecordSelector
     */
    public function findByPage(Page $page): RecordSelector
    {
        return $this->find([Page::REVISION_KEY => $page->primaryKey()]);
    }

    /**
     * Find page last revision.
     *
     * @param Page $page
     * @return null|Revision
     */
    public function findLastForPage(Page $page)
    {
        return $this
            ->findByPage($page)
            ->orderBy([Revision::PRIMARY_KEY => SelectQuery::SORT_DESC])
            ->findOne();
    }

    /**
     * @param Page $page
     * @param \DateTimeInterface $timeStarted
     * @param \DateTimeInterface $timeEnded
     * @return Revision
     */
    public function createFromPage(
        Page $page,
        \DateTimeInterface $timeStarted,
        \DateTimeInterface $timeEnded
    ): Revision
    {
        return $this->create([
            'time_started' => $timeStarted,
            'time_ended'   => $timeEnded,
            'title'        => $page->title,
            'keywords'     => $page->keywords,
            'description'  => $page->description,
            'slug'         => $page->slug,
            'source'       => $page->source,
            'content_hash' => $page->content_hash
        ]);
    }
}