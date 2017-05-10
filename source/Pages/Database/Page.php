<?php

namespace Spiral\Pages\Database;

use Spiral\Models\Accessors\SqlTimestamp;
use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Entities\Relations\HasManyRelation;
use Spiral\Pages\Database\Entities\AbstractPageEntity;
use Spiral\Pages\Database\Types\PageStatus;
use Spiral\Pages\Database\Types\PageType;

/**
 * Class Page
 *
 * @package Spiral\Pages\Database
 *
 * @property SqlTimestamp               $time_created
 * @property SqlTimestamp               $time_updated
 * @property PageStatus                 $status
 * @property PageType                   $type
 * @property int                        $revisions_count
 * @property HasManyRelation|Revision[] revisions
 */
class Page extends AbstractPageEntity
{
    use TimestampsTrait;

    /**
     * {@inheritdoc}
     */
    const ACTIVE_SCHEMA = true;

    const REVISION_KEY = 'page_id';

    /**
     * {@inheritdoc}
     */
    const TABLE = 'pages';

    /**
     * {@inheritdoc}
     */
    const SCHEMA = [
        'status'          => PageStatus::class,
        'type'            => PageType::class, //for future version feature

        //Revisions
        'revisions'       => [
            self::HAS_MANY    => Revision::class,
            Revision::INVERSE => 'page',
            self::OUTER_KEY   => self::REVISION_KEY
        ],
        'revisions_count' => 'int',
    ];

    /**
     * @return bool
     */
    public function hasRevisions(): bool
    {
        return $this->revisions_count > 0;
    }

    /**
     * @param string $status
     * @return bool
     */
    public function setStatus(string $status): bool
    {
        if (in_array($status, PageStatus::ALLOWED_VALUES)) {
            $this->status = $status;

            return true;
        }

        return false;
    }
}