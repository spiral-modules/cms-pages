<?php

namespace TestApplication\Database;

use Spiral\ORM\Record;
use Spiral\Pages\EditorInterface;

/**
 * Class User
 *
 * @package TestApplication\Database
 * @property int    $id
 * @property string $name
 */
class User extends Record implements EditorInterface
{
    /**
     * {@inheritdoc}
     */
    const SCHEMA = [
        'id'   => 'primary',
        'name' => 'string(32)',
    ];

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}