<?php

namespace Spiral\Tests\Pages;

use Spiral\Pages\Config;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Pages;
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
            'metaTags'    => '<meta name="foo" content="bar">',
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

    public function testDraftPage()
    {
        $this->makePage();
        $this->assertCount(1, $this->orm->source(Page::class));

        //No user
        $mock = \Mockery::mock(Permissions::class)->makePartial();
        $mock->shouldReceive('hasContext')->andReturn(false);
        $mock->shouldReceive('canViewDraft');
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(404, $response->getStatusCode());

        //User without permission to view draft
        $mock = \Mockery::mock(Permissions::class)->makePartial();
        $mock->shouldReceive('hasContext');
        $mock->shouldReceive('canViewDraft')->andReturn(false);
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(404, $response->getStatusCode());

        //User with permission to view draft
        $mock = \Mockery::mock(Permissions::class)->makePartial();
        $mock->shouldReceive('hasContext');
        $mock->shouldReceive('canViewDraft')->andReturn(true);
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('some source', (string)$response->getBody());
        $this->assertContains('draft', (string)$response->getBody());
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
        $mock->shouldReceive('hasContext');
        $mock->shouldReceive('canEdit')->andReturn(false);
        $this->container->bind(Permissions::class, $mock);

        $response = $this->get('/some-url');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('some source', (string)$response->getBody());
    }

    public function testRenderEditable()
    {
        $page = $this->makePage();
        $this->assertCount(1, $this->orm->source(Page::class));

        $env = $this->views->getEnvironment()->withDependency('page.editable', function () {
            return true;
        });

        /** @var Config $config */
        $config = $this->container->get(Config::class);
        $crawler = new Crawler($this->views->withEnvironment($env)->render(
            $config->pageView(),
            compact('page')
        ));

        //page metadata
        $title = $crawler->filterXPath('//title')->html();
        $description = $crawler->filterXPath('//meta[@name="description"]')->attr('content');
        $keywords = $crawler->filterXPath('//meta[@name="keywords"]')->attr('content');
        $custom = $crawler->filterXPath('//meta[@name="foo"]')->attr('content');

        $this->assertSame($page->title, $title);
        $this->assertSame($page->description, $description);
        $this->assertSame($page->keywords, $keywords);
        $this->assertSame('bar', $custom);

        //check js
        /** @var Pages $pages */
        $pages = $this->container->get(Pages::class);
        $meta = json_encode($pages->getMeta($page, []));
        $this->assertNotEmpty($crawler->filterXPath('//script'));
        $script = trim($crawler->filterXPath('//script')->html());
        $this->assertSame("window.metadata = $meta;", $script);

        //page div
        $div = $crawler->filterXPath('//div');
        $this->assertGreaterThan(0, $div->count());
        $this->assertSame('html', $div->attr('data-piece'));
        $this->assertEquals($page->primaryKey(), $div->attr('data-id'));
        $this->assertSame($page->source, trim($div->html()));
    }

    public function testRender()
    {
        $page = $this->makePage();
        $this->assertCount(1, $this->orm->source(Page::class));

        $env = $this->views->getEnvironment()->withDependency('page.editable', function () {
            return false;
        });

        /** @var Config $config */
        $config = $this->container->get(Config::class);
        $crawler = new Crawler($this->views->withEnvironment($env)->render(
            $config->pageView(),
            compact('page')
        ));

        //page metadata
        $title = $crawler->filterXPath('//title')->html();
        $description = $crawler->filterXPath('//meta[@name="description"]')->attr('content');
        $keywords = $crawler->filterXPath('//meta[@name="keywords"]')->attr('content');
        $custom = $crawler->filterXPath('//meta[@name="foo"]')->attr('content');

        $this->assertSame($page->title, $title);
        $this->assertSame($page->description, $description);
        $this->assertSame($page->keywords, $keywords);
        $this->assertSame('bar', $custom);

        //check js
        $this->assertEmpty($crawler->filterXPath('//script'));

        //page div
        $div = $crawler->filterXPath('//div');
        $this->assertGreaterThan(0, $div->count());
        $this->assertNotSame('html', $div->attr('data-piece'));
        $this->assertNotEquals($page->primaryKey(), $div->attr('data-id'));
        $this->assertSame($page->source, trim($div->html()));
    }

    public function testRenderWithDefaults()
    {
        $page = $this->makePage();
        $page->keywords = '';
        $page->description = '';
        $page->metaTags = '';
        $page->save();

        $this->assertCount(1, $this->orm->source(Page::class));

        $env = $this->views->getEnvironment()->withDependency('page.editable', function () {
            return false;
        });

        /** @var Config $config */
        $config = $this->container->get(Config::class);
        $crawler = new Crawler($this->views->withEnvironment($env)->render(
            $config->pageView(),
            compact('page')
        ));

        //page metadata
        $title = $crawler->filterXPath('//title')->html();
        $description = $crawler->filterXPath('//meta[@name="description"]')->attr('content');
        $keywords = $crawler->filterXPath('//meta[@name="keywords"]')->attr('content');
        $custom = $crawler->filterXPath('//meta[@name="baz"]')->attr('content');

        $this->assertSame($page->title, $title);
        $this->assertSame('default description', $description);
        $this->assertSame('default,keywords', $keywords);
        $this->assertSame('default tags', $custom);

        //check js
        $this->assertEmpty($crawler->filterXPath('//script'));

        //page div
        $div = $crawler->filterXPath('//div');
        $this->assertGreaterThan(0, $div->count());
        $this->assertNotSame('html', $div->attr('data-piece'));
        $this->assertNotEquals($page->primaryKey(), $div->attr('data-id'));
        $this->assertSame($page->source, trim($div->html()));
    }
}