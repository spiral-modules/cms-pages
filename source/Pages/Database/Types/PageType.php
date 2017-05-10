<?php

namespace Spiral\Pages\Database\Types;

use Spiral\ORM\Columns\EnumColumn;

class PageType extends EnumColumn
{
    /**
     * Types.
     */
    const PAGE    = 'page';
    const VERSION = 'version';

    /**
     * Values.
     * {@inheritdoc}
     */
    const VALUES  = [self::PAGE, self::VERSION];

    /**
     * Default values.
     * {@inheritdoc}
     */
    const DEFAULT = self::PAGE;
}