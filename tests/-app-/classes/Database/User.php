<?php

namespace TestApplication\Database;

use Spiral\ORM\Record;
use Spiral\Pages\EditorInterface;

class User extends Record implements EditorInterface
{
    const SCHEMA   = [
        'id'   => 'primary',
        'name' => 'string(32)',
    ];

    public function getName()
    {
        return $this->name;
    }
}