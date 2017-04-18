<?php

namespace Spiral\Pages\Requests;

use Spiral\Http\Request\RequestFilter;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Requests\Checkers\EntityChecker;

class PageRequest extends RequestFilter
{
    const SCHEMA = [
        'title'       => 'data:title',
        'slug'        => 'data:slug',
        'keywords'    => 'data:keywords',
        'description' => 'data:description',
        'source'      => 'data:source',
    ];

    const VALIDATES = [
        'title'  => [
            'notEmpty',
            [
                'string::shorter',
                250,
                'message' => "[[Title must be less than {0} characters long.]]"
            ],
        ],
        'slug'   => [
            'notEmpty',
            [
                EntityChecker::class . '::isUnique',
                PageSource::class,
                'slug'
            ],
            [
                'string::shorter',
                250,
                'message' => "[[Slug must be less than {0} characters long.]]"
            ],
        ],
        'source' => ['notEmpty'],
    ];

    const SETTERS = [
        'title' => 'trim',
        'slug'  => [self::class, 'trimSlug'],
    ];

    /**
     * @param string $value
     * @return string
     */
    public static function trimSlug($value): string
    {
        return strtolower(trim($value, ' /'));
    }
}