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
     * Get label for status.
     *
     * @param string $status
     * @return null|string
     */
    public function label(string $status)
    {
        if ($this->isListed($status)) {
            return $this->labels[$status];
        }

        return null;
    }

    /**
     * If status is listed
     *
     * @param string $status
     * @return bool
     */
    public function isListed(string $status): bool
    {
        return array_key_exists($status, $this->labels);
    }
}