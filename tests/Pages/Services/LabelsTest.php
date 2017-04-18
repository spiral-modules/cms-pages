<?php

namespace Spiral\Tests\Pages\Services;

use Spiral\Pages\Conditions\GuestOnly;
use Spiral\Pages\Services\Labels\Conditions;
use Spiral\Pages\Services\Labels\Statuses;
use Spiral\Tests\BaseTest;

class LabelsTest extends BaseTest
{
    public function testStatuses()
    {
        /** @var Statuses $labels */
        $labels = $this->container->get(Statuses::class);
        $this->assertNotEmpty($labels->labels());
        $this->assertEmpty($labels->label('some-label'));
        $this->assertFalse($labels->isListed('some-label'));

        $this->assertNotEmpty($labels->label('active'));
        $this->assertTrue($labels->isListed('active'));
    }

    public function testConditions()
    {
        /** @var Statuses $labels */
        $labels = $this->container->get(Conditions::class);
        $this->assertNotEmpty($labels->labels());
        $this->assertEmpty($labels->label('some-label'));
        $this->assertFalse($labels->isListed('some-label'));

        $this->assertNotEmpty($labels->label(GuestOnly::class));
        $this->assertTrue($labels->isListed(GuestOnly::class));
    }
}