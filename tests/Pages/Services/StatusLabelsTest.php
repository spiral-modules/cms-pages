<?php

namespace Spiral\Tests\Pages\Services;

use Spiral\Pages\Services\Labels\Statuses;
use Spiral\Tests\BaseTest;

class StatusLabelsTest extends BaseTest
{
    public function testList()
    {
        /** @var Statuses $labels */
        $labels = $this->container->get(Statuses::class);
        $this->assertNotEmpty($labels->labels());
    }

    public function testHasLabel()
    {
        /** @var Statuses $labels */
        $labels = $this->container->get(Statuses::class);

        $this->assertEmpty($labels->label('some-label'));
        $this->assertNotEmpty($labels->label('active'));
    }

    public function testIsListed()
    {
        /** @var Statuses $labels */
        $labels = $this->container->get(Statuses::class);
        $this->assertNotEmpty($labels->labels());

        $this->assertFalse($labels->isListed('some-label'));
        $this->assertTrue($labels->isListed('active'));
    }
}