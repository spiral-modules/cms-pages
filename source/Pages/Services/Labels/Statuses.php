<?php

namespace Spiral\Pages\Services\Labels;

class Statuses
{
    /** @var array */
    protected $labels = [
        'active' => 'Active',
        'draft'  => 'Draft',
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