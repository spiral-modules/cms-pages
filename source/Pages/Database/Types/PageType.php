<?php

namespace Spiral\Pages\Database\Types;

use Spiral\ORM\Columns\EnumColumn;

class PageType extends EnumColumn
{
    /**
     * Types.
     */
    const PAGE  = 'active';
    const VERSION   = 'draft';

    /**
     * Values.
     */
    const VALUES  = [self::PAGE, self::VERSION];

    /**
     * Default values.
     */
    const DEFAULT = self::PAGE;
}