<?php

namespace Spiral\Tests\Pages;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Permissions;
use Spiral\Tests\BaseTest;
use Spiral\Tests\HttpTest;
use Symfony\Component\DomCrawler\Crawler;

class MiddlewareTest extends HttpTest
{
    /**
     * @return Page
     */
    protected function makePage(): Page
    {
        $page = $this->orm->source(Page::class)->create([
            'title'       => 'title',
            'keywords'    => 'keyword1,keyword2',
            'description' => 'description',
            'slug'        => 'some-url',
            'source'      => '<p>some source</p>',
        ]);
        $page->save();

        return $page;
    }

    public function testWithoutPage()
    {
        $this->assertCount(0, $this->orm->source(Page::class));

        $response = $this->get('/some-url');
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testDraftPageNoUser()
    {
        $this->makePage();
        $this->assertCount(1, $this->orm->source(Page::class));

        $mock = \Mockery::mock(Permissions::class)->makePartial();
        $mock->shouldReceive('hasContext')->andReturn(false);
        $mock->shouldReceive('canViewDraft');
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testDraftPageUserWithoutPermissions()
    {
        $this->makePage();
        $this->assertCount(1, $this->orm->source(Page::class));

        $mock = \Mockery::mock(Permissions::class)->makePartial();
        $mock->shouldReceive('hasContext')->andReturn(true);
        $mock->shouldReceive('canViewDraft')->andReturn(false);
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testDraftPageUserWithPermissions()
    {
        $this->makePage();
        $this->assertCount(1, $this->orm->source(Page::class));

        $mock = \Mockery::mock(Permissions::class)->makePartial();
        $mock->shouldReceive('hasContext')->andReturn(true);
        $mock->shouldReceive('canViewDraft')->andReturn(true);
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('some source', (string)$response->getBody());
    }

    public function testActivePageNoUser()
    {
        $page = $this->makePage();
        $page->status->setActive();
        $page->save();
        $this->assertCount(1, $this->orm->source(Page::class));

        $mock = \Mockery::mock(Permissions::class)->makePartial();
        $mock->shouldReceive('hasContext')->andReturn(false);
        $mock->shouldReceive('canViewDraft');
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('some source', (string)$response->getBody());
    }

    public function testActivePageUser()
    {
        $page = $this->makePage();
        $page->status->setActive();
        $page->save();
        $this->assertCount(1, $this->orm->source(Page::class));

        $mock = \Mockery::mock(Permissions::class)->makePartial();
        $mock->shouldReceive('hasContext')->andReturn(true);
        $mock->shouldReceive('canEdit')->andReturn(false);
        $mock->shouldReceive('canViewDraft')->andReturn(false);
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(200, $response->getStatusCode());
        print_r((string)$response->getBody());
        $this->assertContains('some source', (string)$response->getBody());
    }

//todo add render tests
//    public function testEditableRendering()
//    {
//        $env = $this->views->getEnvironment()->withDependency('cms.editable', function () {
//            return true;
//        });
//
//        $crawler = new Crawler($this->views->withEnvironment($env)->render('default'));
//
//        // page metadata
//        $title = $crawler->filterXPath('//title')->html();
//        $description = $crawler->filterXPath('//meta[@name="description"]')->attr('content');
//        $keywords = $crawler->filterXPath('//meta[@name="keywords"]')->attr('content');
//        $custom = $crawler->filterXPath('//meta[@name="foo"]')->attr('content');
//
//        $this->assertSame('Title', $title);
//        $this->assertSame('Description', $description);
//        $this->assertSame('Keywords', $keywords);
//        $this->assertSame('Bar', $custom);
//
//        // check js
//        $meta = json_encode($this->orm->source(PageMeta::class)->findOne());
//        $script = trim($crawler->filterXPath('//script')->html());
//        $this->assertSame("window.metadata = $meta;", $script);
//
//        // piece div
//        $div = $crawler->filterXPath('//div');
//        $this->assertGreaterThan(0, $div->count());
//        $this->assertSame('html', $div->attr('data-piece'));
//        $this->assertSame('sample-piece', $div->attr('data-id'));
//        $this->assertSame('Sample.', trim($div->html()));
//    }

//    public function testRendering()
//    {
//        $env = $this->views->getEnvironment()->withDependency('cms.editable', function () {
//            return false;
//        });
//
//        $crawler = new Crawler($this->views->withEnvironment($env)->render('default'));
//
//        // page metadata
//        $title = $crawler->filterXPath('//title')->html();
//        $description = $crawler->filterXPath('//meta[@name="description"]')->attr('content');
//        $keywords = $crawler->filterXPath('//meta[@name="keywords"]')->attr('content');
//        $custom = $crawler->filterXPath('//meta[@name="foo"]')->attr('content');
//        $this->assertSame('Title', $title);
//        $this->assertSame('Description', $description);
//        $this->assertSame('Keywords', $keywords);
//        $this->assertSame('Bar', $custom);
//
//        // piece div
//        $div = $crawler->filterXPath('//body');
//        $this->assertGreaterThan(0, $div->count());
//        $this->assertSame('Sample.', trim($div->html()));
//    }
}