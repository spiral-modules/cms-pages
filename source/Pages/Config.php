<?php
namespace Spiral\Pages;

use Spiral\Core\InjectableConfig;

class Config extends InjectableConfig
{
    /**
     * Configuration section.
     */
    const CONFIG = 'modules/pages';

    /**
     * {@inheritdoc}
     */
    protected $config = [
        'fields' => [
            'slug',
            'title',
            'description',
            'source',
            'keywords'
        ],
        'page'   => 'pages:page'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return $this->config['fields'];
    }

    /**
     * @return bool
     */
    public function withKeywords(): bool
    {
        return !empty($this->config['with']['keywords']);
    }

    /**
     * @return string
     */
    public function page(): string
    {
        return $this->config['page'];
    }
}