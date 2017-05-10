<?php

namespace Spiral\Tests\Pages\Database;

use Spiral\Models\Accessors\AbstractTimestamp;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Database\Types\PageStatus;
use Spiral\Pages\Database\Types\PageType;
use Spiral\Tests\BaseTest;

class EntitiesTest extends BaseTest
{
    public function testPageFillable()
    {
        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();
        $page->setFields([
            'slug'           => 'slug',
            'title'          => 'title',
            'description'    => 'description',
            'keywords'       => 'keywords',
            'source'         => 'source',
            'metaTags'       => 'metaTags',
            'content_hash'   => 'content_hash',
            'type'           => PageType::VERSION, //not default value
            'status'         => PageStatus::ACTIVE, //not default value
            'revision_count' => 3,
        ]);

        $this->assertSame('slug', $page->slug);
        $this->assertSame('title', $page->title);
        $this->assertSame('description', $page->description);
        $this->assertSame('keywords', $page->keywords);
        $this->assertSame('source', $page->source);
        $this->assertSame('metaTags', $page->metaTags);

        //not fillable below
        $this->assertNotSame('content_hash', $page->content_hash);
        $this->assertSame(PageType::DEFAULT, $page->type->packValue()); //default value
        $this->assertSame(PageStatus::DEFAULT, $page->status->packValue()); //default value
        $this->assertSame(0, $page->revisions_count);
        $this->assertFalse($page->hasRevisions());

        $page->content_hash = 'content_hash';
        $page->type->setValue(PageType::VERSION);
        $page->status->setValue(PageStatus::ACTIVE);
        $page->revisions_count = 1;

        $this->assertSame('content_hash', $page->content_hash);
        $this->assertSame(PageType::VERSION, $page->type->packValue());
        $this->assertSame(PageStatus::ACTIVE, $page->status->packValue());
        $this->assertSame(1, $page->revisions_count);
        $this->assertTrue($page->hasRevisions());
    }

    public function testRevisionFillable()
    {
        $datetime1 = new \DateTime('yesterday');
        $datetime2 = new \DateTime('now');

        /** @var Revision $revision */
        $revision = $this->orm->source(Revision::class)->create();
        $revision->setFields([
            'slug'         => 'slug',
            'title'        => 'title',
            'description'  => 'description',
            'keywords'     => 'keywords',
            'source'       => 'source',
            'metaTags'     => 'metaTags',
            'time_started' => $datetime1,
            'time_ended'   => $datetime2,
            'content_hash' => 'content_hash',
            'diff'         => 'diff',
        ]);

        $this->assertSame('slug', $revision->slug);
        $this->assertSame('title', $revision->title);
        $this->assertSame('description', $revision->description);
        $this->assertSame('keywords', $revision->keywords);
        $this->assertSame('source', $revision->source);
        $this->assertSame('metaTags', $revision->metaTags);
        $this->assertSame(
            $datetime1->format(AbstractTimestamp::DEFAULT_FORMAT),
            $revision->time_started->format(AbstractTimestamp::DEFAULT_FORMAT)
        );
        $this->assertSame(
            $datetime2->format(AbstractTimestamp::DEFAULT_FORMAT),
            $revision->time_ended->format(AbstractTimestamp::DEFAULT_FORMAT)
        );

        //not fillable below
        $this->assertNotSame('content_hash', $revision->content_hash);
        $this->assertNotSame('diff', $revision->diff);

        $revision->content_hash = 'content_hash';
        $revision->diff = 'diff';

        $this->assertSame('content_hash', $revision->content_hash);
        $this->assertSame('diff', $revision->diff);
    }

    public function testSetPageStatus()
    {
        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $this->assertEmpty($page->setStatus('some-status'));

        $this->assertNotEmpty($page->setStatus(PageStatus::ACTIVE));
        $this->assertTrue($page->status->isActive());

        $this->assertNotEmpty($page->setStatus(PageStatus::DRAFT));
        $this->assertTrue($page->status->isDraft());

        $this->assertEmpty($page->setStatus(PageStatus::DELETED));
        $this->assertFalse($page->status->isDeleted());

        $page->status->setDeleted();
        $this->assertTrue($page->status->isDeleted());
    }
}