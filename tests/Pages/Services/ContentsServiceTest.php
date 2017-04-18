<?php

namespace Spiral\Tests\Pages\Services;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Services\UniquePageContents;
use Spiral\Tests\BaseTest;

class ContentsServiceTest extends BaseTest
{
    public function testContentID()
    {
        /** @var UniquePageContents $service */
        $service = $this->container->get(UniquePageContents::class);

        /**
         * @var Page $page
         */
        $page = $this->orm->source(Page::class)->create([
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'slug',
            'source'      => '<p>source</p>',
        ]);
        $this->assertNotEmpty($service->contentID($page));
    }

    public function testIdentical()
    {
        /** @var UniquePageContents $service */
        $service = $this->container->get(UniquePageContents::class);

        /**
         * @var Page $page
         * @var Page $page2
         * @var Page $page3
         */
        $page = $this->orm->source(Page::class)->create([
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'slug',
            'source'      => '<p>source</p>',
        ]);
        $page->content_hash = $service->contentID($page);

        $page2 = $this->orm->source(Page::class)->create([
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'slug',
            'source'      => '<p>source</p>',
        ]);
        $page2->content_hash = $service->contentID($page2);

        $page3 = $this->orm->source(Page::class)->create([
            'title'       => 'title2',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description2',
            'slug'        => 'slug',
            'source'      => '<p>source</p>',
        ]);
        $page3->content_hash = $service->contentID($page3);

        $this->assertTrue($service->contentsIdentical($page, $page2));
        $this->assertFalse($service->contentsIdentical($page, $page3));
    }

    public function testDiff()
    {
        /** @var UniquePageContents $service */
        $service = $this->container->get(UniquePageContents::class);

        /**
         * @var Page $page
         * @var Page $page2
         * @var Page $page3
         */
        $page = $this->orm->source(Page::class)->create([
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'slug',
            'source'      => '<p>source</p>',
        ]);
        $page->content_hash = $service->contentID($page);

        $page2 = $this->orm->source(Page::class)->create([
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'slug',
            'source'      => '<p>source</p>',
        ]);
        $page2->content_hash = $service->contentID($page2);

        $page3 = $this->orm->source(Page::class)->create([
            'title'       => 'title2',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description2',
            'slug'        => 'slug',
            'source'      => '<p>source</p>',
        ]);
        $page3->content_hash = $service->contentID($page3);

        $this->assertEmpty($service->calcDiff($page, $page2));
        $this->assertNotEmpty($service->calcDiff($page, $page3));
    }
}