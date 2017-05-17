<?php

namespace Spiral\Tests\Pages\Database\Sources;

use Spiral\Models\Accessors\AbstractTimestamp;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Database\Sources\RevisionSource;
use Spiral\Tests\BaseTest;

class RevisionSourceTest extends BaseTest
{
    public function testFindByPage()
    {
        /**
         * @var Page           $page
         * @var Revision       $revision
         * @var Revision       $revision2
         * @var RevisionSource $source
         */
        $page = $this->orm->source(Page::class)->create();
        $source = $this->container->get(RevisionSource::class);

        $page->revisions->add($source->create());
        $page->save();

        $revision = $source->create();
        $revision->save();

        $this->assertCount(1, $this->orm->source(Page::class));
        $this->assertCount(2, $source);
        $this->assertCount(1, $source->findByPage($page));

        $revision2 = $source->findByPage($page)->findOne();
        $this->assertNotEmpty($revision2);
        $this->assertSame($page->primaryKey(), $revision2->page->primaryKey());
    }

    public function testFindLastForPage()
    {
        /**
         * @var Page           $page
         * @var Revision       $revision
         * @var Revision       $revision2
         * @var RevisionSource $source
         */
        $page = $this->orm->source(Page::class)->create();
        $source = $this->container->get(RevisionSource::class);

        $revision = $source->create();
        $revision->save();

        $revision2 = $source->create();

        $page->revisions->add($source->create());
        $page->revisions->add($revision2);
        $page->save();

        $this->assertCount(1, $this->orm->source(Page::class));
        $this->assertCount(3, $source);
        $this->assertCount(2, $source->findByPage($page));

        $revision3 = $source->findLastForPage($page);
        $this->assertNotEmpty($revision3);
        $this->assertSame($page->primaryKey(), $revision3->page->primaryKey());
        $this->assertSame($revision3->primaryKey(), $revision2->primaryKey());
    }

    public function testCreateFromPage()
    {
        /**
         * @var Page           $page
         * @var Revision       $revision
         * @var RevisionSource $source
         */
        $page = $this->orm->source(Page::class)->create();
        $source = $this->container->get(RevisionSource::class);

        $page->title = 'title';
        $page->keywords = 'keyword1,keyword2';
        $page->description = 'description';
        $page->slug = 'slug';
        $page->source = '<p>source</p>';
        $page->content_hash = 'asd786asdnlj';

        $start = new \DateTime('-2 weeks');
        $end = new \DateTime('-1 weeks');
        $revision = $source->createFromPage($page, $start, $end);

        $this->assertSame($page->title, $revision->title);
        $this->assertSame($page->keywords, $revision->keywords);
        $this->assertSame($page->description, $revision->description);
        $this->assertSame($page->slug, $revision->slug);
        $this->assertSame($page->source, $revision->source);
        $this->assertSame($page->source, $revision->source);
        $this->assertSame(
            $start->format(AbstractTimestamp::DEFAULT_FORMAT),
            $revision->time_started->format(AbstractTimestamp::DEFAULT_FORMAT)
        );
        $this->assertSame(
            $end->format(AbstractTimestamp::DEFAULT_FORMAT),
            $revision->time_ended->format(AbstractTimestamp::DEFAULT_FORMAT)
        );
    }
}