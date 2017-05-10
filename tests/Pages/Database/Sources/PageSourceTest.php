<?php

namespace Spiral\Tests\Pages\Database\Sources;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Database\Types\PageStatus;
use Spiral\Tests\BaseTest;

class PageSourceTest extends BaseTest
{
    public function testFindByPK()
    {
        /**
         * @var Page       $page
         * @var Page       $page1
         * @var Page       $page2
         * @var Page       $page3
         * @var PageSource $source
         */
        $source = $this->container->get(PageSource::class);

        $page1 = $source->create();
        $page1->status->setDraft();
        $page1->save();

        $page2 = $source->create();
        $page2->status->setActive();
        $page2->save();

        $page3 = $source->create();
        $page3->status->setDeleted();
        $page3->save();

        $page = $source->findByPK($page1->primaryKey());
        $this->assertNotEmpty($page);
        $this->assertSame($page->primaryKey(), $page1->primaryKey());

        $page = $source->findByPK($page2->primaryKey());
        $this->assertNotEmpty($page);
        $this->assertSame($page->primaryKey(), $page2->primaryKey());

        $page = $source->findByPK($page3->primaryKey());
        $this->assertEmpty($page);
    }

    public function testFindOne()
    {
        /**
         * @var Page       $page
         * @var PageSource $source
         */
        $source = $this->container->get(PageSource::class);

        $page = $source->create();
        $page->status->setDraft();
        $page->save();

        $this->assertNotEmpty($source->findOne());
        $this->assertEmpty($source->findOne(['status' => PageStatus::ACTIVE]));
        $this->assertNotEmpty($source->findOne(['status' => PageStatus::DRAFT]));
        $this->assertEmpty($source->findOne(['status' => PageStatus::DELETED]));

        $page->status->setActive();
        $page->save();

        $this->assertNotEmpty($source->findOne());
        $this->assertNotEmpty($source->findOne(['status' => PageStatus::ACTIVE]));
        $this->assertEmpty($source->findOne(['status' => PageStatus::DRAFT]));
        $this->assertEmpty($source->findOne(['status' => PageStatus::DELETED]));

        $page->status->setDeleted();
        $page->save();

        $this->assertEmpty($source->findOne());
        $this->assertEmpty($source->findOne(['status' => PageStatus::ACTIVE]));
        $this->assertEmpty($source->findOne(['status' => PageStatus::DRAFT]));
        $this->assertEmpty($source->findOne(['status' => PageStatus::DELETED]));
    }

    public function testFind()
    {
        /**
         * @var Page       $page
         * @var PageSource $source
         */
        $source = $this->container->get(PageSource::class);

        $page = $source->create();
        $page->status->setDraft();
        $page->save();

        $page = $source->create();
        $page->status->setActive();
        $page->save();

        $page = $source->create();
        $page->status->setDeleted();
        $page->save();

        $this->assertCount(2, $source->find());
        $this->assertCount(1, $source->find(['status' => PageStatus::ACTIVE]));
        $this->assertCount(1, $source->find(['status' => PageStatus::DRAFT]));
        $this->assertCount(0, $source->find(['status' => PageStatus::DELETED]));
    }

    public function testFindBySlug()
    {
        /**
         * @var Page       $page
         * @var PageSource $source
         */
        $source = $this->container->get(PageSource::class);

        $page = $source->create();
        $page->status->setDraft();
        $page->slug = 'slug';
        $page->save();

        //Test status of pages to be found
        $this->assertEmpty($source->findBySlug('slug'));

        $found = $source->findBySlug('slug', false);
        $this->assertNotEmpty($found);
        $this->assertSame($page->primaryKey(), $found->primaryKey());

        $page->status->setDeleted();
        $page->save();

        $this->assertEmpty($source->findBySlug('slug'));
        $this->assertEmpty($source->findBySlug('slug', false));

        $page->status->setActive();
        $page->save();

        $this->assertNotEmpty($source->findBySlug('slug'));
        $this->assertNotEmpty($source->findBySlug('slug', false));

        $this->assertNotEmpty($source->findBySlug('slug/'));
        $this->assertNotEmpty($source->findBySlug('/slug/'));
        $this->assertNotEmpty($source->findBySlug('/slug'));
    }

    public function testCreateFromRevision()
    {
        /**
         * @var Page       $page
         * @var Revision   $revision
         * @var PageSource $source
         */
        $revision = $this->orm->source(Revision::class)->create();
        $source = $this->container->get(PageSource::class);

        $revision->title = 'title';
        $revision->keywords = 'keyword1,keyword2';
        $revision->description = 'description';
        $revision->slug = 'slug';
        $revision->source = '<p>source</p>';

        $page = $source->createFromRevision($revision);

        $this->assertSame($page->title, $revision->title);
        $this->assertSame($page->keywords, $revision->keywords);
        $this->assertSame($page->description, $revision->description);
        $this->assertSame($page->slug, $revision->slug);
        $this->assertSame($page->source, $revision->source);
    }
}