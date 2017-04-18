<?php

namespace Spiral\Tests\Pages\Services;

use Spiral\Pages\Services\HashService;
use Spiral\Tests\BaseTest;

class HashTest extends BaseTest
{
    public function testEquals()
    {
        /** @var HashService $service */
        $service = $this->container->get(HashService::class);

        $hash1 = $hash2 = md5(time());
        $hash3 = 'some-hash';

        $this->assertTrue($service->compareHashes($hash1, $hash2));
        $this->assertFalse($service->compareHashes($hash1, $hash3));
    }
}