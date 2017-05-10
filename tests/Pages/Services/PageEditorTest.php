<?php

namespace Spiral\Tests\Pages\Services;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Requests\PageRequest;
use Spiral\Pages\Services\PageEditor;
use Spiral\Tests\BaseTest;
use TestApplication\Database\User;

class PageEditorTest extends BaseTest
{
    public function testSetFields()
    {
        $this->assertCount(0, $this->orm->source(Page::class));
        $this->assertCount(0, $this->orm->source(Revision::class));

        /** @var PageEditor $service */
        $service = $this->container->get(PageEditor::class);

        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $request = new PageRequest();
        $request->setField('title', 'title');
        $request->setField('keywords', 'keyword1,keyword2');
        $request->setField('description', 'description');
        $request->setField('slug', 'some-url');
        $request->setField('source', '<p>some source</p>');

        $service->setFields($page, $request, $this->editor());
        $page->save();

        $this->assertCount(1, $this->orm->source(Page::class));

        $request = new PageRequest();
        $request->setField('title', 'title2');
        $request->setField('keywords', 'keyword3,keyword4');
        $request->setField('description', 'description2');
        $request->setField('slug', 'some-url2');
        $request->setField('source', '<p>some source2</p>');

        $service->setFields($page, $request, $this->editor());
        $page->save();

        $this->assertCount(1, $this->orm->source(Page::class));
        $this->assertCount(1, $this->orm->source(Revision::class));
    }

    public function testDelete()
    {
        $this->assertCount(0, $this->orm->source(Page::class));

        /** @var PageEditor $service */
        $service = $this->container->get(PageEditor::class);

        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $request = new PageRequest();
        $request->setField('title', 'title');
        $request->setField('keywords', 'keyword1,keyword2');
        $request->setField('description', 'description');
        $request->setField('slug', 'some-url');
        $request->setField('source', '<p>some source</p>');

        $service->setFields($page, $request, $this->editor());
        $page->save();

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

        /** @var PageEditor $service */
        $service = $this->container->get(PageEditor::class);

        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();

        $request = new PageRequest();
        $request->setField('title', 'title');
        $request->setField('keywords', 'keyword1,keyword2');
        $request->setField('description', 'description');
        $request->setField('slug', 'some-url');
        $request->setField('source', '<p>some source</p>');

        $service->setFields($page, $request, $this->editor());
        $page->save();

        $this->assertCount(1, $this->orm->source(Page::class));

        $request = new PageRequest();
        $request->setField('title', 'title2');
        $request->setField('keywords', 'keyword3,keyword4');
        $request->setField('description', 'description2');
        $request->setField('slug', 'some-url2');
        $request->setField('source', '<p>some source2</p>');

        $service->setFields($page, $request, $this->editor());
        $page->save();

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

        $service->rollbackRevision($page, $revision, $this->editor());
        $page->save();

        $this->assertSame('title', $page->title);
        $this->assertSame('keyword1,keyword2', $page->keywords);
        $this->assertSame('description', $page->description);
        $this->assertSame('some-url', $page->slug);
        $this->assertSame('<p>some source</p>', $page->source);

        $this->assertCount(1, $this->orm->source(Page::class));
        $this->assertCount(2, $this->orm->source(Revision::class));
    }

    protected function editor()
    {
        $editor = $this->orm->source(User::class)->findOne();
        if (empty($editor)) {
            $editor = new User();
            $editor->name = 'Editor';
            $editor->save();
        }

        return $editor;
    }
}