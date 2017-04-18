<?php

namespace Spiral\Tests\Pages;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Pages;
use Spiral\Tests\BaseTest;

class PagesTest extends BaseTest
{
    public function testPages()
    {
        /** @var Pages $pages */
        $pages = $this->container->get(Pages::class);

        $this->assertEmpty($pages->find('slug'));

        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create(['slug' => 'slug']);
        $page->setStatus('active');
        $page->save();

        $this->assertNotEmpty($pages->find('slug'));
        $this->assertNotEmpty($pages->find('Slug'));
    }
}