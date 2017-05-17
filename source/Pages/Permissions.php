<?php

namespace Spiral\Pages;

use Spiral\Auth\ContextInterface;
use Spiral\Core\ContainerInterface;
use Spiral\Core\Service;
use Spiral\Security\Traits\GuardedTrait;

class Permissions extends Service
{
    use GuardedTrait;

    /** @var ContainerInterface */
    protected $container;

    /** @var Config */
    protected $config;

    /**
     * Pages constructor.
     *
     * @param Config             $config
     * @param ContainerInterface $container
     */
    public function __construct(Config $config, ContainerInterface $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * @return bool
     */
    public function canEdit(): bool
    {
        // robots can't edit :(
        if (!$this->hasContext()) {
            return false;
        }

        return $this->allows($this->config->editCMSPermission());
    }

    /**
     * @return bool
     */
    public function canViewDraft(): bool
    {
        if (!$this->hasContext()) {
            return false;
        }

        return $this->allows($this->config->viewDraftPermission());
    }

    /**
     * @return bool
     */
    public function hasContext(): bool
    {
        return $this->container->has(ContextInterface::class);
    }
}