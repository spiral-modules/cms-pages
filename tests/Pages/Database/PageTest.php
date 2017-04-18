<?php

namespace Spiral\Tests\Pages\Database;


use Spiral\Pages\Conditions\AuthorizedOnly;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Tests\BaseTest;

class PageTest extends BaseTest
{
    public function testHasMethods()
    {
        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();
        $this->assertEmpty($page->hasRevisions());
        $this->assertEmpty($page->hasVersions());

        $page->versions_count = 2;
        $page->revisions_count = 3;

        $this->assertNotEmpty($page->hasRevisions());
        $this->assertNotEmpty($page->hasVersions());
    }

    public function testSetStatus()
    {
        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $this->assertEmpty($page->setStatus('some-status'));
        $this->assertNotEmpty($page->setStatus('active'));
        $this->assertTrue($page->status->isActive());
        $this->assertNotEmpty($page->setStatus('draft'));
        $this->assertTrue($page->status->isDraft());
        $this->assertEmpty($page->setStatus('deleted'));
        $this->assertFalse($page->status->isDeleted());

        $page->status->setDeleted();
        $this->assertTrue($page->status->isDeleted());
    }

    public function testConditions()
    {
        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $this->assertEmpty($page->conditions());

        $page->conditions = AuthorizedOnly::class;
        $this->assertCount(1, $page->conditions());
    }
}