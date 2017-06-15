<?php

namespace Spiral\Pages\Requests\Api;

use Spiral\Http\Request\RequestFilter;
use Spiral\Pages\Requests\PageRequestInterface;
use Spiral\Tokenizer\Isolator;

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
    const SCHEMA    = ['source' => 'data:data.html',];
    const VALIDATES = ['source' => ['notEmpty'],];
    const SETTERS   = ['source' => 'trim',];
}