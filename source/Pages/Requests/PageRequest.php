<?php

namespace Spiral\Pages\Requests;

use Spiral\Http\Request\RequestFilter;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Database\Types\PageStatus;
use Spiral\Pages\Requests\Checkers\EntityChecker;

/**
 * Class PageRequest
 *
 * @package Spiral\Pages\Requests
 * @property string $status
 */
class PageRequest extends RequestFilter implements PageRequestInterface
{
    /**
     * {@inheritdoc}
     */
    const SCHEMA = [
        'title'       => 'data:title',
        'slug'        => 'data:slug',
        'keywords'    => 'data:keywords',
        'description' => 'data:description',
        'source'      => 'data:source',
        'metaTags'    => 'data:metaTags',
        'status'      => 'data:status',
    ];

    /**
     * {@inheritdoc}
     */
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
        'status' => [
            'notEmpty',
            [
                'in_array',
                PageStatus::ALLOWED_VALUES,
                'message' => "[[Invalid status value.]]"
            ],
        ],
        'source' => ['notEmpty'],
    ];

    /**
     * {@inheritdoc}
     */
    const SETTERS = [
        'title'       => 'trim',
        'slug'        => [self::class, 'slugSetter'],
        'keywords'    => 'trim',
        'description' => 'trim',
        'source'      => 'trim',
        'metaTags'    => 'trim',
    ];

    /**
     * @param string $value
     * @return string
     */
    public static function slugSetter($value): string
    {
        return strtolower(trim($value, ' /'));
    }
}