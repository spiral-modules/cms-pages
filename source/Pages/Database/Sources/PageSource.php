<?php

namespace Spiral\Pages\Database\Sources;

use Spiral\ORM\Entities\RecordSelector;
use Spiral\ORM\Entities\RecordSource;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Database\Types\PageStatus;
use Spiral\Pages\Database\Types\PageType;

class PageSource extends RecordSource
{
    const RECORD = Page::class;

    /**
     * {@inheritdoc}
     * @return null|Page
     */
    public function findByPK($id, array $load = [])
    {
        /** @var Page $entity */
        $entity = parent::findByPK($id, $load);
        if (empty($entity) || $entity->status->isDeleted()) {
            return null;
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     * @return null|Page
     */
    public function findOne(array $query = [], array $sortBy = [], array $load = [])
    {
        return parent::find($this->notDeletedClause())->orderBy($sortBy)->load($load)->findOne($query);
    }

    /**
     * {@inheritdoc}
     * @return RecordSelector
     */
    public function find(array $query = []): RecordSelector
    {
        return parent::find($query)->andWhere($this->notDeletedClause());
    }

    /**
     * All find methods should use it to skip deleted pages.
     *
     * @return array
     */
    protected function notDeletedClause(): array
    {
        return ['status' => ['!=' => PageStatus::DELETED]];
    }

    /**
     * Find page by a slug.
     *
     * @param string $slug
     * @return Page|null
     */
    public function findBySlug(string $slug)
    {
        $query = [
            'slug'   => strtolower(trim($slug, ' /')),
            'status' => PageStatus::ACTIVE,
            'type'   => PageType::PAGE
        ];

        return $this->findOne($query);
    }

    /**
     * Create page from revision.
     *
     * @param Revision $revision
     * @return Page
     */
    public function createFromRevision(Revision $revision): Page
    {
        return $this->create([
            'title'       => $revision->title,
            'keywords'    => $revision->keywords,
            'description' => $revision->description,
            'slug'        => $revision->slug,
            'source'      => $revision->source,
        ]);
    }

    /**
     * Find page versions.
     *
     * @param Page $page
     * @return RecordSelector
     */
    //public function findByParentPage(Page $page): RecordSelector
    //{
    //    return $this->find([Page::VERSION_KEY => $page->primaryKey()]);
    //}
}