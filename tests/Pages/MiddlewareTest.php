<?php

namespace Spiral\Tests\Pages;

use Spiral\Pages\Database\Page;
use Spiral\Tests\BaseTest;
use Spiral\Tests\HttpTest;

class MiddlewareTest extends HttpTest
{
    /**
     * @expectedExceptionCode 404
     */
    public function testWithoutPage()
    {
        $this->assertCount(0, $this->orm->source(Page::class));
        $this->get('/some-url');
    }

    public function testWithPage()
    {
        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create([
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'some-url',
            'source'      => '<p>some source</p>',
        ]);
        $page->setStatus('active');
        $page->save();
        $this->assertCount(1, $this->orm->source(Page::class));

        $response = $this->get('/some-url');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('some source', (string)$response->getBody());
    }
}