<?php

namespace Spiral\Pages\Services;

use Spiral\Core\Service;
use Spiral\Pages\EditorInterface;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Utils;

class PageManager extends Service
{
    /** @var PageSource */
    private $pageSource;

    /** @var RevisionsService */
    private $revisions;

    /** @var Utils */
    private $utils;

    /** @var UniquePageContents */
    private $contents;

    /**
     * PageService constructor.
     *
     * @param PageSource         $source
     * @param RevisionsService   $revisions
     * @param Utils              $utils
     * @param UniquePageContents $contents
     */
    public function __construct(
        PageSource $source,
        RevisionsService $revisions,
        Utils $utils,
        UniquePageContents $contents
    ) {
        $this->pageSource = $source;
        $this->revisions = $revisions;
        $this->utils = $utils;
        $this->contents = $contents;
    }

    /**
     * Set fields to page from request, add revision if required.
     *
     * @param Page                 $page
     * @param array                $request
     * @param EditorInterface|null $editor
     */
    public function setFields(
        Page $page,
        array $request,
        EditorInterface $editor = null
    ) {
        if (!$page->primaryKey()) {
            $this->setFieldsFromRequest($page, $request, $editor);
            $page->save();
        } else {
            $prev = clone $page;

            $this->setFieldsFromRequest($page, $request, $editor);

            if (!$this->contents->contentsIdentical($page, $prev)) {
                $this->addRevision($page, $prev);
            }
        }
    }

    /**
     * Rollback to a given revision.
     * If contents are not identical - create new revision for a previous page content state.
     *
     * @param Page                 $page
     * @param Revision             $revision
     * @param EditorInterface|null $editor
     */
    public function rollbackRevision(Page $page, Revision $revision, EditorInterface $editor = null)
    {
        $prev = clone $page;

        $page->setFields([
            'title'       => $revision->title,
            'keywords'    => $revision->keywords,
            'description' => $revision->description,
            'slug'        => $revision->slug,
            'source'      => $revision->source,
        ]);

        $page->editor = $editor;
        $page->content_hash = $this->contents->contentID($page);

        if (!$this->contents->contentsIdentical($page, $prev)) {
            $this->addRevision($page, $prev);
        }
    }

    /**
     * Delete page.
     *
     * @param Page $page
     */
    public function delete(Page $page)
    {
        $page->status->setDeleted();
        $page->save();
    }

    /**
     * Add new revision to a page.
     *
     * @param Page $page
     * @param Page $prev
     */
    protected function addRevision(Page $page, Page $prev)
    {
        $revision = $this->revisions->makeRevision($page, $prev);

        $page->revisions_count++;
        $page->revisions->add($revision);
    }

    /**
     * Set request fields, editor to page and update its content hash.
     *
     * @param Page                 $page
     * @param array                $request
     * @param EditorInterface|null $editor
     */
    protected function setFieldsFromRequest(
        Page $page,
        array $request,
        EditorInterface $editor = null
    ) {
        $page->setFields($this->utils->fetchKeys(
            $request,
            ['title', 'description', 'keywords', 'source', 'slug']
        ));

        $page->editor = $editor;
        $page->content_hash = $this->contents->contentID($page);
    }
}