<?php

namespace Spiral\Pages\Database;

use Spiral\Models\Accessors\SqlTimestamp;
use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Entities\Relations\HasManyRelation;
use Spiral\Pages\Database\Entities\AbstractPageEntity;
use Spiral\Pages\Database\Types\PageStatus;
use Spiral\Pages\Database\Types\PageType;
use Spiral\Pages\PageConditionInterface;

/**
 * Class Page
 *
 * @package Spiral\Pages\Database
 *
 * @property SqlTimestamp               $time_created
 * @property SqlTimestamp               $time_updated
 * @property PageStatus                 $status
 * @property string                     $type
 * @property int                        $versions_count
 * @property int                        $revisions_count
 * @property string                     $conditions
 * @property HasManyRelation|Revision[] revisions
 * @property HasManyRelation|Page[]     versions
 */
class Page extends AbstractPageEntity
{
    use TimestampsTrait;

    const ACTIVE_SCHEMA = true;

    const PRIMARY_KEY  = 'id';
    const REVISION_KEY = 'page_id';
    const VERSION_KEY  = 'parent_id';

    const TABLE = 'pages';

    const SCHEMA = [
        'status'          => PageStatus::class,
        'type'            => PageType::class,

        //Revisions
        'revisions'       => [
            self::HAS_MANY    => Revision::class,
            Revision::INVERSE => 'page',
            self::OUTER_KEY   => self::REVISION_KEY
        ],
        'revisions_count' => 'int',

        //Versions
        'versions'        => [
            self::HAS_MANY  => self::class,
            self::OUTER_KEY => self::VERSION_KEY
        ],
        'versions_count'  => 'int',

        //Added conditions, will be ignored if no versions added or no matches were found,
        //Can't add version without conditions added (main page and all versions should have them)
        'conditions'      => 'text',
    ];

    /**
     * @return bool
     */
    public function hasRevisions(): bool
    {
        return $this->revisions_count > 0;
    }

    /**
     * @return bool
     */
    public function hasVersions(): bool
    {
        return $this->versions_count > 0;
    }

    /**
     * @return PageConditionInterface[]
     */
    public function conditions(): array
    {
        $conditions = [];
        foreach (explode(',', $this->conditions) as $condition) {
            $condition = trim($condition, ' ,');
            if (empty($condition)) {
                continue;
            }

            $conditions[$condition] = new $condition;
        }

        return $conditions;
    }

    /**
     * @param string $status
     * @return bool
     */
    public function setStatus(string $status): bool
    {
        if (in_array($status, [PageStatus::ACTIVE, PageStatus::DRAFT])) {
            $this->status = $status;

            return true;
        }

        return false;
    }
}