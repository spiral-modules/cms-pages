<?php

namespace Spiral\Tests\Pages\Services;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Services\PageManager;
use Spiral\Tests\BaseTest;

class PageManagerTest extends BaseTest
{
    public function testSetFields()
    {
        $this->assertCount(0, $this->orm->source(Page::class));
        $this->assertCount(0, $this->orm->source(Revision::class));

        /** @var PageManager $service */
        $service = $this->container->get(PageManager::class);

        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $service->setFieldsAndSave($page, [
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'some-url',
            'source'      => '<p>some source</p>',
        ], null);

        $this->assertCount(1, $this->orm->source(Page::class));

        $service->setFieldsAndSave($page, [
            'title'       => 'title2',
            'keywords'    => 'keyword3,keyword4',
            'description' => 'description2',
            'slug'        => 'some-url2',
            'source'      => '<p>some source2</p>',
        ], null);

        $this->assertCount(1, $this->orm->source(Page::class));
        $this->assertCount(1, $this->orm->source(Revision::class));
    }

    public function testDelete()
    {
        $this->assertCount(0, $this->orm->source(Page::class));

        /** @var PageManager $service */
        $service = $this->container->get(PageManager::class);

        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $service->setFieldsAndSave($page, [
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'some-url',
            'source'      => '<p>some source</p>',
        ], null);


        $this->assertCount(1, $this->orm->source(Page::class));
        $this->assertTrue($page->status->isDraft());

        $service->delete($page);

        $this->assertCount(1, $this->orm->source(Page::class));
        $this->assertTrue($page->status->isDeleted());
    }

    public function testRollback()
    {
        $this->assertCount(0, $this->orm->source(Page::class));
        $this->assertCount(0, $this->orm->source(Revision::class));

        /** @var PageManager $service */
        $service = $this->container->get(PageManager::class);

        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $service->setFieldsAndSave($page, [
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'some-url',
            'source'      => '<p>some source</p>',
        ], null);

        $this->assertCount(1, $this->orm->source(Page::class));

        $service->setFieldsAndSave($page, [
            'title'       => 'title2',
            'keywords'    => 'keyword3,keyword4',
            'description' => 'description2',
            'slug'        => 'some-url2',
            'source'      => '<p>some source2</p>',
        ], null);

        $this->assertCount(1, $this->orm->source(Page::class));
        $this->assertCount(1, $this->orm->source(Revision::class));

        /** @var Revision $revision */
        $revision = $this->orm->source(Revision::class)->findOne();

        $this->assertSame('title', $revision->title);
        $this->assertSame('keyword1,keyword2', $revision->keywords);
        $this->assertSame('description', $revision->description);
        $this->assertSame('some-url', $revision->slug);
        $this->assertSame('<p>some source</p>', $revision->source);

        $this->assertSame('title2', $page->title);
        $this->assertSame('keyword3,keyword4', $page->keywords);
        $this->assertSame('description2', $page->description);
        $this->assertSame('some-url2', $page->slug);
        $this->assertSame('<p>some source2</p>', $page->source);

        $service->rollbackRevision($page, $revision);

        $this->assertSame('title', $page->title);
        $this->assertSame('keyword1,keyword2', $page->keywords);
        $this->assertSame('description', $page->description);
        $this->assertSame('some-url', $page->slug);
        $this->assertSame('<p>some source</p>', $page->source);
    }
}