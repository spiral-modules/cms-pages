<?php

namespace Spiral\Tests\Pages;

use Spiral\Pages\Config;
use Spiral\Pages\Utils;
use Spiral\Tests\BaseTest;

class ClassesTest extends BaseTest
{
    public function testConfig()
    {
        /** @var Config $config */
        $config = $this->container->get(Config::class);

        $this->assertNotEmpty($config->fields());
        $this->assertNotEmpty($config->pageView());
        $this->assertNotEmpty($config->editCMSPermission());
        $this->assertNotEmpty($config->viewDraftPermission());
        $this->assertNotEmpty($config->showDraftNotice());
    }

    public function testUtils()
    {
        /** @var Utils $utils */
        $utils = $this->container->get(Utils::class);
        $data = [
            'k1' => 'v1',
            'k2' => 'v2',
            'k3' => 'v3'
        ];

        $this->assertEmpty($utils->fetchKeys($data, []));
        $this->assertEquals(['k1' => 'v1', 'k2' => 'v2'], $utils->fetchKeys($data, ['k1', 'k2']));
        $this->assertEquals(['k1' => 'v1'], $utils->fetchKeys($data, ['k1', 'k4']));
    }
}