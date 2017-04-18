<?php

namespace Spiral\Pages\Database;

use Spiral\ORM\Record;

class Condition extends Record
{
    const TABLE    = 'conditions';
    const DATABASE = 'pages';

    const SCHEMA = [
        'id'    => 'primary',
        'name'  => 'string',
        'label' => 'string'
    ];
}