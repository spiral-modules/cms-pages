<?php

namespace Spiral\Pages\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spiral\Core\Component;
use Spiral\Core\ContainerInterface;
use Spiral\Http\Exceptions\ClientException;
use Spiral\Http\MiddlewareInterface;
use Spiral\Pages\Config;
use Spiral\Pages\Pages;
use Spiral\Pages\Services;
use Spiral\Views\ViewsInterface;

class RenderPageMiddleware extends Component implements MiddlewareInterface
{
    /**
     * @invisible
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        /**
         * @var Pages  $finder
         * @var Config $config
         */
        $finder = $this->container->get(Pages::class);
        $config = $this->container->get(Config::class);
        $uri = $request->getUri()->getPath();

        try {
            return $next($request, $response);
        } catch (ClientException $exception) {
            if ($exception->getCode() !== 404) {
                throw new $exception;
            }

            $page = $finder->find($uri, $request);

            if (empty($page)) {
                throw new $exception;
            }
        }

        $scope = $this->container->replace(Request::class, $request);
        try {
            /** @var ViewsInterface $views */
            $views = $this->container->get(ViewsInterface::class);

            $response->getBody()->write(
                $views->render($config->page(), compact('page'))
            );

            return $response;
        } finally {
            $this->container->restore($scope);
        }
    }
}