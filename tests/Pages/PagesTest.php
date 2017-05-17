<?php

namespace Spiral\Tests\Pages;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Pages;
use Spiral\Tests\BaseTest;

class PagesTest extends BaseTest
{
    public function testFind()
    {
        /**
         * @var Page       $page
         * @var PageSource $source
         * @var Pages      $pages
         */
        $source = $this->container->get(PageSource::class);
        $pages = $this->container->get(Pages::class);

        $page = $source->create();
        $page->status->setDraft();
        $page->slug = 'slug';
        $page->save();

        //Test status of pages to be found
        $this->assertEmpty($pages->find('slug'));

        $found = $pages->find('slug', false);
        $this->assertNotEmpty($found);
        $this->assertSame($page->primaryKey(), $found->primaryKey());

        $page->status->setDeleted();
        $page->save();

        $this->assertEmpty($pages->find('slug'));
        $this->assertEmpty($pages->find('slug', false));

        $page->status->setActive();
        $page->save();

        $this->assertNotEmpty($pages->find('slug'));
        $this->assertNotEmpty($pages->find('slug', false));

        $this->assertNotEmpty($pages->find('slug/'));
        $this->assertNotEmpty($pages->find('/slug/'));
        $this->assertNotEmpty($pages->find('/slug'));
    }

    public function testGetMeta()
    {
        /**
         * @var Page       $page
         * @var PageSource $source
         * @var Pages      $pages
         */
        $source = $this->container->get(PageSource::class);
        $pages = $this->container->get(Pages::class);

        $page = $source->create();

        $this->assertNotEmpty($pages->getMeta($page, []));

        $meta = $pages->getMeta($page, [
            'keywords'    => 'some keywords',
            'description' => 'some description',
            'metaTags'    => 'some metaTags',
        ]);
        $this->assertEmpty($meta['title']);
        $this->assertSame($meta['keywords'], 'some keywords');
        $this->assertSame($meta['description'], 'some description');
        $this->assertSame($meta['metaTags'], 'some metaTags');

        $page->title = 'title';
        $page->keywords = 'keywords';
        $page->description = 'description';
        $page->metaTags = 'metaTags';
        $page->slug = 'slug';

        $this->assertNotEmpty($pages->getMeta($page, []));

        $meta = $pages->getMeta($page, [
            'keywords'    => 'some keywords',
            'description' => 'some description',
            'metaTags'    => 'some metaTags',
        ]);
        $this->assertSame($meta['title'], 'title');
        $this->assertSame($meta['keywords'], 'keywords');
        $this->assertSame($meta['description'], 'description');
        $this->assertSame($meta['metaTags'], 'metaTags');
    }
}