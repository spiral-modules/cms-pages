<?php

namespace Spiral\Pages\Database\Types;

use Spiral\ORM\Columns\EnumColumn;

class PageStatus extends EnumColumn
{
    /**
     * Statuses.
     */
    const ACTIVE  = 'active';
    const DRAFT   = 'draft';
    const DELETED = 'deleted';

    /**
     * Values.
     */
    const VALUES  = [self::ACTIVE, self::DRAFT, self::DELETED];

    /**
     * Default values.
     */
    const DEFAULT = self::DRAFT;

    /**
     * If status is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->packValue() == self::ACTIVE;
    }

    /**
     * If status is draft.
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->packValue() == self::DRAFT;
    }

    /**
     * If status is deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->packValue() == self::DELETED;
    }

    /**
     * Set aactive status.
     */
    public function setActive()
    {
        $this->setValue(self::ACTIVE);
    }

    /**
     * Set draft status.
     */
    public function setDraft()
    {
        $this->setValue(self::DRAFT);
    }

    /**
     * Set deleted status.
     */
    public function setDeleted()
    {
        $this->setValue(self::DELETED);
    }
}