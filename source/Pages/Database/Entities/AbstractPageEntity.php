<?php

namespace Spiral\Pages\Database\Entities;

use Spiral\ORM\Record;
use Spiral\Pages\EditorInterface;

/**
 * Class AbstractPageEntity
 *
 * @package Spiral\Pages\Database\Entities
 *
 * @property string          $slug
 * @property string          $title
 * @property string          $description
 * @property string          $keywords
 * @property string          $source
 * @property string          $metaTags
 * @property string          $content_hash
 * @property EditorInterface $editor
 */
abstract class AbstractPageEntity extends Record
{
    /**
     * Will be activated in child classes.
     *
     * {@inheritdoc}
     */
    const ACTIVE_SCHEMA = false;

    /**
     * {@inheritdoc}
     */
    const DATABASE = 'pages';

    /**
     * {@inheritdoc}
     */
    const SCHEMA = [
        'id'           => 'primary',
        'slug'         => 'string',
        'title'        => 'string',
        'description'  => 'text',
        'keywords'     => 'text',
        'source'       => 'text',
        'metaTags'     => 'text',
        'content_hash' => 'string',
        'editor'       => [self::BELONGS_TO_MORPHED => EditorInterface::class]
    ];

    /**
     * {@inheritdoc}
     */
    const FILLABLE = [
        'title',
        'description',
        'keywords',
        'source',
        'slug',
        'metaTags'
    ];
}