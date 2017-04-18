<?php

namespace Spiral\Pages\Database;

use Spiral\Models\Accessors\SqlTimestamp;
use Spiral\ORM\Entities\Relations\HasOneRelation;
use Spiral\Pages\Database\Entities\AbstractPageEntity;

/**
 * Class Revision
 *
 * @package Spiral\Pages\Database
 * @property SqlTimestamp        $time_started
 * @property SqlTimestamp        $time_ended
 * @property HasOneRelation|Page $page
 * @property string              $diff
 */
class Revision extends AbstractPageEntity
{
    const PRIMARY_KEY   = 'id';
    const ACTIVE_SCHEMA = true;

    const TABLE = 'revisions';

    const SCHEMA = [
        'time_started'    => 'datetime',
        'time_ended'      => 'datetime',
        'diff'            => 'string',
    ];

    const FILLABLE = ['time_started', 'time_ended'];
}