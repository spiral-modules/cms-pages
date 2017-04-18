<?php

namespace Spiral\Pages\Services;

use Spiral\Core\Service;
use Spiral\ORM\Transaction;
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
    public function setFieldsAndSave(
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

            if ($this->contents->contentsIdentical($page, $prev)) {
                $page->save();
            } else {
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

        if ($this->contents->contentsIdentical($page, $prev)) {
            $page->save();
        } else {
            $this->addRevision($page, $prev);
        }
    }

    /**
     * Delete page with all versions.
     *
     * @param Page $page
     */
    public function delete(Page $page)
    {
        $transaction = new Transaction();

        $page->status->setDeleted();
        $transaction->store($page);

        if ($page->hasVersions()) {
            foreach ($page->versions() as $version) {
                $version->status->setDeleted();
                $transaction->store($version);
            }
        }

        $transaction->run();
    }

    /**
     * Add new revision to a page.
     *
     * @param Page $page
     * @param Page $prev
     */
    protected function addRevision(Page $page, Page $prev)
    {
        $page->revisions_count++;
        $revision = $this->revisions->makeRevision($page, $prev);

        $transaction = new Transaction();
        $transaction->store($page);
        $transaction->store($revision);
        $transaction->run();
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