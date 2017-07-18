<?php

namespace Spiral\Pages\Bootloaders;

use Psr7Middlewares\Middleware\Payload;
use Spiral\Core\Bootloaders\Bootloader;
use Spiral\Http\HttpDispatcher;
use Spiral\Http\Routing\Route;

class PagesBootloader extends Bootloader
{
    /**
     * Requested to be bootloaded.
     */
    const BOOT = true;

    /**
     * Spiral will automatically populate requested method injections for boot method.
     *
     * @param HttpDispatcher $http
     */
    public function boot(HttpDispatcher $http)
    {
        $route = new Route(
            'api_pages',
            'api/pages/<action>/<id>',
            'Spiral\Pages\Controllers\ApiController::<action>'
        );

        $http->addRoute($route->withMiddleware(Payload::class));
    }
}
