<?php

namespace Spiral\Pages\Services;

use Spiral\Core\Service;
use Spiral\Pages\EditorInterface;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Requests\PageRequestInterface;

class PageEditor extends Service
{
    /** @var RevisionsService */
    private $revisions;

    /** @var UniquePageContents */
    private $contents;

    /**
     * PageService constructor.
     *
     * @param RevisionsService   $revisions
     * @param UniquePageContents $contents
     */
    public function __construct(RevisionsService $revisions, UniquePageContents $contents)
    {
        $this->revisions = $revisions;
        $this->contents = $contents;
    }

    /**
     * Set fields to page from request, add revision if required.
     *
     * @param Page                 $page
     * @param PageRequestInterface $request
     * @param EditorInterface      $editor
     */
    public function setFields(Page $page, PageRequestInterface $request, EditorInterface $editor)
    {
        if (!$page->primaryKey()) {
            $this->setRequest($page, $request, $editor);
        } else {
            $prev = clone $page;

            $this->setRequest($page, $request, $editor);

            if (!$this->contents->contentsIdentical($page, $prev)) {
                $this->addRevision($page, $prev);
            }
        }
    }

    /**
     * Rollback to a given revision.
     * If contents are not identical - create new revision for a previous page content state.
     *
     * @param Page            $page
     * @param Revision        $revision
     * @param EditorInterface $editor
     */
    public function rollbackRevision(Page $page, Revision $revision, EditorInterface $editor)
    {
        $prev = clone $page;

        $this->setRevision($page, $revision, $editor);

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
     * Set request fields from a given request, editor and update its content hash.
     *
     * @param Page                 $page
     * @param PageRequestInterface $request
     * @param EditorInterface      $editor
     */
    protected function setRequest(
        Page $page,
        PageRequestInterface $request,
        EditorInterface $editor
    ) {
        $page->setFields($request);
        $page->editor = $editor;
        $page->content_hash = $this->contents->contentID($page);
    }

    /**
     * Set fields from a given revision, editor and update its content hash.
     *
     * @param Page            $page
     * @param Revision        $revision
     * @param EditorInterface $editor
     */
    protected function setRevision(Page $page, Revision $revision, EditorInterface $editor)
    {
        $page->setFields([
            'title'       => $revision->title,
            'keywords'    => $revision->keywords,
            'description' => $revision->description,
            'metaTags'    => $revision->metaTags,
            'slug'        => $revision->slug,
            'source'      => $revision->source,
        ]);

        $page->editor = $editor;
        $page->content_hash = $this->contents->contentID($page);
    }
}