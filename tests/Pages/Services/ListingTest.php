<?php

namespace Spiral\Tests\Pages\Services;

use Spiral\Listing\Bootloaders\ListingsBootloader;
use Spiral\Listing\Listing;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Services\ListingService;
use Spiral\Tests\BaseTest;

class ListingTest extends BaseTest
{
    public function testEquals()
    {
        /** @var ListingService $service */
        $service = $this->container->get(ListingService::class);
        $this->app->getBootloader()->bootload([ListingsBootloader::class]);

        $this->assertInstanceOf(Listing::class, $service->pagesListing(
            $this->orm->source(Page::class)->find()
        ));
        $this->assertInstanceOf(Listing::class, $service->revisionsListing(
            $this->orm->source(Revision::class)->find()
        ));
    }
}