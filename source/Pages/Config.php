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
        'unique-content-fields' => [
            'slug',
            'title',
            'description',
            'source',
            'keywords',
            'metaTags'
        ],
        'page'                  => '',
        'editCMSPermission'     => 'vault.pages.editCMS',
        'viewDraftPermission'   => 'vault.pages.viewDraft',
    ];

    /**
     * Fields that define unique page content (for revisions).
     *
     * @return array
     */
    public function fields(): array
    {
        return $this->config['unique-content-fields'];
    }

    /**
     * Page view filename.
     *
     * @return string
     */
    public function pageView(): string
    {
        return $this->config['page'];
    }

    /**
     * Allows edit page by redaxtor.
     *
     * @return string
     */
    public function editCMSPermission(): string
    {
        return $this->config['editCMSPermission'];
    }

    /**
     * Allows view draft page by admin users.
     *
     * @return string
     */
    public function viewDraftPermission(): string
    {
        return $this->config['viewDraftPermission'];
    }
}