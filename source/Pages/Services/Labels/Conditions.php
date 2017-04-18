<?php

namespace Spiral\Pages\Services\Labels;

use Spiral\Pages\Conditions\AuthorizedOnly;
use Spiral\Pages\Conditions\DefaultLocaleOnly;
use Spiral\Pages\Conditions\GuestOnly;
use Spiral\Pages\Conditions\EnLocaleOnly;

/**
 * Class ConditionsMatcher
 *
 * @package Spiral\Pages
 */
class Conditions
{
    /** @var array */
    protected $labels = [
        AuthorizedOnly::class    => 'Only for authorized users',
        GuestOnly::class         => 'Only for guests',
        DefaultLocaleOnly::class => 'Only for default locale',
        EnLocaleOnly::class      => 'Only for en_EN locale',
    ];

    /**
     * @return array
     */
    public function labels(): array
    {
        return $this->labels;
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function label(string $name)
    {
        if ($this->isListed($name)) {
            return $this->labels[$name];
        }

        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isListed(string $name): bool
    {
        return array_key_exists($name, $this->labels);
    }
}