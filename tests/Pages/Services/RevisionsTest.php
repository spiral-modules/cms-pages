<?php

namespace Spiral\Tests\Pages\Services;

use Spiral\Models\Accessors\AbstractTimestamp;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Services\HashService;
use Spiral\Pages\Services\RevisionsService;
use Spiral\Tests\BaseTest;

class RevisionsTest extends BaseTest
{
    public function testEquals()
    {
        /** @var RevisionsService $service */
        $service = $this->container->get(RevisionsService::class);

        $this->assertCount(0, $this->orm->source(Revision::class));

        /**
         * @var Page $page
         * @var Page $prev
         */
        $page = $this->orm->source(Page::class)->create([
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'slug',
            'source'      => '<p>source</p>',
        ]);
        $page->time_created = new \DateTime('-2 days');
        $page->save();

        $prev = clone $page;
        $page->setFields([
            'title'       => 'title2',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description2',
            'slug'        => 'slug',
            'source'      => '<p>source</p>'
        ]);

        $revision = $service->makeRevision($page, $prev);

        $this->assertCount(0, $this->orm->source(Revision::class));

        $this->assertSame($prev->title, $revision->title);
        $this->assertSame($prev->keywords, $revision->keywords);
        $this->assertSame($prev->description, $revision->description);
        $this->assertSame($prev->slug, $revision->slug);
        $this->assertSame($prev->source, $revision->source);
        $this->assertSame($prev->content_hash, $revision->content_hash);
        $this->assertSame(
            $prev->time_created->format(AbstractTimestamp::DEFAULT_FORMAT),
            $revision->time_started->format(AbstractTimestamp::DEFAULT_FORMAT)
        );
        $this->assertSame(
            (new \DateTime())->format(AbstractTimestamp::DEFAULT_FORMAT),
            $revision->time_ended->format(AbstractTimestamp::DEFAULT_FORMAT)
        );

        $revision->save();

        $prev = clone $page;
        $page->setFields([
            'title'       => 'title3',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description3',
            'slug'        => 'slug',
            'source'      => '<p>source</p>'
        ]);
        $page->save();

        $revision1 = $service->makeRevision($page, $prev);

        $this->assertSame($prev->title, $revision1->title);
        $this->assertSame($prev->keywords, $revision1->keywords);
        $this->assertSame($prev->description, $revision1->description);
        $this->assertSame($prev->slug, $revision1->slug);
        $this->assertSame($prev->source, $revision1->source);
        $this->assertSame($prev->content_hash, $revision1->content_hash);
        $this->assertSame(
            $revision->time_ended->format(AbstractTimestamp::DEFAULT_FORMAT),
            $revision1->time_started->format(AbstractTimestamp::DEFAULT_FORMAT)
        );
        $this->assertSame(
            (new \DateTime())->format(AbstractTimestamp::DEFAULT_FORMAT),
            $revision1->time_ended->format(AbstractTimestamp::DEFAULT_FORMAT)
        );
    }
}