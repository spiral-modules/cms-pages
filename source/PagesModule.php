<?php

namespace Spiral;

use Spiral\Core\DirectoriesInterface;
use Spiral\Modules\ModuleInterface;
use Spiral\Modules\PublisherInterface;
use Spiral\Modules\RegistratorInterface;
use Spiral\Pages\Config;

/**
 * Class PagesModule
 *
 * @package Spiral
 */
class PagesModule implements ModuleInterface
{
    /**
     * @inheritDoc
     */
    public function register(RegistratorInterface $registrator)
    {
        //Register tokenizer directory
        $registrator->configure('tokenizer', 'directories', 'spiral/pages', [
            "directory('libraries') . 'spiral/pages/source/Pages/Database/',",
        ]);

        //Register view namespace
        $registrator->configure('views', 'namespaces.spiral', 'spiral/pages', [
            "'pages' => [",
            "directory('libraries') . 'spiral/pages/source/views/',",
            "/*{{namespaces.pages}}*/",
            "],",
        ]);

        //Register database settings
        $registrator->configure('databases', 'databases', 'spiral/pages', [
            "'pages' => [",
            "   'connection'  => 'mysql',",
            "   'tablePrefix' => 'cms_pages_'",
            "   /*{{databases.pages}}*/",
            "],",
        ]);

        //Register controller in vault config
        $registrator->configure('modules/vault', 'controllers', 'spiral/pages', [
            "'pages' => \\Spiral\\Pages\\Controllers\\PagesController::class,",
        ]);

        //Register view environment variables
        $registrator->configure('views', 'environment', 'spiral/pages', [
            "'page.editable'     => [\\Spiral\\Pages\\Permissions::class, 'canEdit'],",
            "'page.canViewDraft' => [\\Spiral\\Pages\\Permissions::class, 'canViewDraft'],",
        ]);
    }

    /**
     * @inheritDoc
     */
    public function publish(PublisherInterface $publisher, DirectoriesInterface $directories)
    {
        //Publish config
        $publisher->publish(
            __DIR__ . '/config/config.php',
            $directories->directory('config') . Config::CONFIG . '.php',
            PublisherInterface::FOLLOW
        );
    }
}