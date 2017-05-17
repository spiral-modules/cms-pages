<?php

namespace Spiral\Pages\Requests\Api;

use Spiral\Http\Request\RequestFilter;
use Spiral\Pages\Requests\PageRequestInterface;

/**
 * Class SourceRequest
 *
 * @package Spiral\Pages\Requests
 *
 * @property string $source
 */
class SourceRequest extends RequestFilter implements PageRequestInterface
{
    /**
     * @var array
     */
    const SCHEMA = [
        'source' => 'data:source',
    ];

    /**
     * @var array
     */
    const VALIDATES = [
        'source' => ['notEmpty'],
    ];

    /**
     * {@inheritdoc}
     */
    const SETTERS = [
        'source' => 'trim',
    ];
}