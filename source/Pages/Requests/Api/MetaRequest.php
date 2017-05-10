<?php

namespace Spiral\Pages\Requests\Api;

use Spiral\Http\Request\RequestFilter;
use Spiral\Pages\Requests\PageRequestInterface;

/**
 * Class MetaRequest
 *
 * @package Spiral\Pages\Requests
 *
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $metaTags
 */
class MetaRequest extends RequestFilter implements PageRequestInterface
{
    /**
     * {@inheritdoc}
     */
    const SCHEMA = [
        'title'       => 'data:title',
        'description' => 'data:description',
        'keywords'    => 'data:keywords',
        'metaTags'    => 'data:metaTags',
    ];

    /**
     * {@inheritdoc}
     */
    const VALIDATES = [
        'title' => [
            'notEmpty',
            [
                'string::shorter',
                250,
                'message' => "[[Title must be less than {0} characters long.]]"
            ],
        ]
    ];

    /**
     * {@inheritdoc}
     */
    const SETTERS = [
        'title'       => 'trim',
        'keywords'    => 'trim',
        'description' => 'trim',
        'metaTags'    => 'trim',
    ];
}