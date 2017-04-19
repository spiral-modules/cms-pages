<?php

namespace Spiral\Tests\Pages\Database;

use Spiral\Pages\Database\Page;
use Spiral\Tests\BaseTest;

class PageTest extends BaseTest
{
    public function testHasMethods()
    {
        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();
        $this->assertEmpty($page->hasRevisions());

        $page->revisions_count = 3;

        $this->assertNotEmpty($page->hasRevisions());
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
}