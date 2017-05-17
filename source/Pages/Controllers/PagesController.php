<?php

namespace Spiral\Pages\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Spiral\Core\Controller;
use Spiral\Http\Exceptions\ClientExceptions\BadRequestException;
use Spiral\Http\Exceptions\ClientExceptions\NotFoundException;
use Spiral\Http\Request\InputManager;
use Spiral\Http\Response\ResponseWrapper;
use Spiral\Pages\EditorInterface;
use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Revision;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Database\Sources\RevisionSource;
use Spiral\Pages\EditorProvider;
use Spiral\Pages\Requests\Checkers\EntityChecker;
use Spiral\Pages\Requests\PageRequest;
use Spiral\Pages\Services\ListingService;
use Spiral\Pages\Services\PageEditor;
use Spiral\Pages\Services\Labels\Statuses;
use Spiral\Security\Traits\GuardedTrait;
use Spiral\Translator\Traits\TranslatorTrait;
use Spiral\Vault\Vault;
use Spiral\Views\ViewManager;

/**
 * Class AbstractPagesController
 * Controller is abstract due to it is required to define editor instance.
 *
 * @package Spiral\Pages\Controllers
 *
 * @property InputManager    $input
 * @property ViewManager     $views
 * @property Vault           $vault
 * @property ResponseWrapper $response
 */
class PagesController extends Controller
{
    use GuardedTrait, TranslatorTrait;

    const GUARD_NAMESPACE = 'vault.pages';

    /**
     * Pages list.
     *
     * @param ListingService $listings
     * @param PageSource     $source
     * @param Statuses       $statuses
     * @return string
     */
    public function indexAction(
        ListingService $listings,
        PageSource $source,
        Statuses $statuses
    ) {
        return $this->views->render('pages:vault/list', [
            'listing'  => $listings->pagesListing($source->find()),
            'statuses' => $statuses->labels()
        ]);
    }

    /**
     * Create new page.
     *
     * @param Statuses   $statuses
     * @param PageSource $source
     * @return string
     */
    public function addAction(Statuses $statuses, PageSource $source)
    {
        $this->allows('add');

        return $this->views->render('pages:vault/create', [
            'statuses' => $statuses->labels(),
            'page'     => $source->create()
        ]);
    }

    /**
     * Create page from another one.
     *
     * @param string     $id
     * @param Statuses   $statuses
     * @param PageSource $source
     * @return string
     */
    public function createFromPageAction($id, Statuses $statuses, PageSource $source)
    {
        $page = $source->findByPK($id);
        if (empty($page)) {
            throw new NotFoundException();
        }

        $this->allows('add');

        return $this->views->render('pages:vault/create', [
            'statuses'   => $statuses->labels(),
            'page'       => $page,
            'isCopy'     => true,
            'PageSource' => $page
        ]);
    }

    /**
     * Create page from revision.
     *
     * @param string         $id
     * @param Statuses       $statuses
     * @param PageSource     $source
     * @param RevisionSource $revisionSource
     * @return string
     */
    public function createFromRevisionAction(
        $id,
        Statuses $statuses,
        PageSource $source,
        RevisionSource $revisionSource
    ) {
        /** @var Revision $revision */
        $revision = $revisionSource->findByPK($id);
        if (empty($revision)) {
            throw new NotFoundException();
        }

        $this->allows('add');

        $page = $source->createFromRevision($revision);

        return $this->views->render('pages:vault/create', [
            'statuses'   => $statuses->labels(),
            'page'       => $page,
            'isCopy'     => true,
            'PageSource' => $revision
        ]);
    }

    /**
     * View page revision.
     *
     * @param string         $id
     * @param RevisionSource $source
     * @return string
     */
    public function viewRevisionAction($id, RevisionSource $source)
    {
        /** @var Revision $revision */
        $revision = $source->findByPK($id);
        if (empty($revision)) {
            throw new NotFoundException();
        }

        $this->allows('viewRevision', ['entity' => $revision]);

        return $this->views->render('pages:vault/revision', compact('revision'));
    }

    /**
     * Edit page.
     *
     * @param string         $id
     * @param ListingService $listings
     * @param PageSource     $source
     * @param Statuses       $statuses
     * @param RevisionSource $revisionSource
     * @return string
     */
    public function editAction(
        $id,
        ListingService $listings,
        PageSource $source,
        Statuses $statuses,
        RevisionSource $revisionSource
    ) {
        $page = $source->findByPK($id);
        if (empty($page)) {
            throw new NotFoundException();
        }

        $this->allows('view', ['entity' => $page]);

        return $this->views->render('pages:vault/edit', [
            'page'      => $page,
            'revisions' => $listings->revisionsListing($revisionSource->findByPage($page)),
            'statuses'  => $statuses->labels()
        ]);
    }

    /**
     * Do action with a page, change page status.
     *
     * @param string                 $id
     * @param PageSource             $source
     * @param ServerRequestInterface $request
     * @return array|\Psr\Http\Message\ResponseInterface
     */
    public function actionAction($id, PageSource $source, ServerRequestInterface $request)
    {
        $page = $source->findByPK($id);
        if (empty($page)) {
            throw new NotFoundException();
        }

        $this->allows('update', ['entity' => $page]);

        if ($page->setStatus($this->input->query('status'))) {
            $page->save();
        } else {
            throw new BadRequestException();
        }

        if ($this->input->isAjax()) {
            return [
                'status'  => 200,
                'message' => $this->say('Page status changed.'),
                'action'  => 'refresh'
            ];
        } else {
            return $this->response->redirect($this->actionPageRedirect($request));
        }
    }

    /**
     * Delete page.
     *
     * @param string                 $id
     * @param PageEditor             $service
     * @param PageSource             $source
     * @param ServerRequestInterface $request
     * @return array|\Psr\Http\Message\ResponseInterface
     */
    public function deleteAction(
        $id,
        PageEditor $service,
        PageSource $source,
        ServerRequestInterface $request
    ) {
        $page = $source->findByPK($id);
        if (empty($page)) {
            throw new NotFoundException();
        }

        $this->allows('delete', ['entity' => $page]);

        $service->delete($page);

        if ($this->input->isAjax()) {
            return [
                'status'  => 200,
                'message' => $this->say('Page deleted.'),
                'action'  => 'refresh'
            ];
        } else {
            return $this->response->redirect($this->deletePageRedirect($page, $request));
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\UriInterface|string
     */
    protected function actionPageRedirect(ServerRequestInterface $request)
    {
        $serverParams = $request->getServerParams();
        if (empty($serverParams['HTTP_REFERER'])) {
            //Came from nowhere
            return $this->vault->uri('pages');
        }

        return $serverParams['HTTP_REFERER'];
    }

    /**
     * @param Page                   $page
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\UriInterface|string
     */
    protected function deletePageRedirect(Page $page, ServerRequestInterface $request)
    {
        $serverParams = $request->getServerParams();
        if (empty($serverParams['HTTP_REFERER'])) {
            //Came from nowhere
            return $this->vault->uri('pages');
        }

        $referrer = rtrim($serverParams['HTTP_REFERER'], '/');
        $self = rtrim($this->vault->uri('pages:edit', ['id' => $page->primaryKey()]), '/');
        $parsed = parse_url($referrer);

        if (strcasecmp($referrer, $self) === 0 || strcasecmp($parsed['path'], $self) === 0) {
            //Came from edit page, we can't redirect back here
            return $this->vault->uri('pages');
        } else {
            return $referrer;
        }
    }

    /**
     * Update page.
     *
     * @param string          $id
     * @param PageEditor      $service
     * @param PageSource      $source
     * @param PageRequest     $request
     * @param EditorInterface $editor
     * @return array
     */
    public function updateAction(
        $id,
        PageEditor $service,
        PageSource $source,
        PageRequest $request,
        EditorInterface $editor
    ) {
        $page = $source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Page not found.')
            ];
        }

        $this->allows('update', ['entity' => $page]);

        $request->setField(EntityChecker::ENTITY_FIELD, $page);
        if (!$request->isValid()) {
            return [
                'status' => 400,
                'errors' => $request->getErrors()
            ];
        }

        $service->setFields($page, $request, $editor);
        $page->setStatus($request->status);
        $page->save();

        return [
            'status'  => 200,
            'message' => $this->say('Page updated.')
        ];
    }

    /**
     * Update page to a given revision.
     *
     * @param string          $id
     * @param RevisionSource  $source
     * @param PageEditor      $service
     * @param EditorInterface $editor
     * @return array|\Psr\Http\Message\ResponseInterface
     */
    public function applyRevisionAction(
        $id,
        RevisionSource $source,
        PageEditor $service,
        EditorInterface $editor
    ) {
        /** @var Revision $revision */
        $revision = $source->findByPK($id);
        if (empty($revision)) {
            throw new NotFoundException();
        }

        $page = $revision->page;
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Page not found.')
            ];
        }

        $this->allows('applyRevision', ['entity' => $page]);

        $service->rollbackRevision($page, $revision, $editor);
        $page->save();

        $uri = $this->vault->uri('pages:edit', ['id' => $page->primaryKey()]);
        if ($this->input->isAjax()) {
            return [
                'status'  => 200,
                'message' => $this->say('Page rolled back.'),
                'action'  => ['redirect' => $uri]
            ];
        } else {
            return $this->response->redirect($uri);
        }
    }

    /**
     * @param PageEditor      $service
     * @param PageSource      $source
     * @param PageRequest     $request
     * @param EditorInterface $editor
     * @return array
     */
    public function createAction(
        PageEditor $service,
        PageSource $source,
        PageRequest $request,
        EditorInterface $editor
    ) {
        $this->allows('add');

        if (!$request->isValid()) {
            return [
                'status' => 400,
                'errors' => $request->getErrors()
            ];
        }

        /** @var Page $page */
        $page = $source->create();

        $service->setFields($page, $request, $editor);
        $page->setStatus($request->status);
        $page->save();

        return [
            'status' => 201,
            'action' => [
                'redirect' => $this->vault->uri('pages:edit', ['id' => $page->primaryKey()])
            ]
        ];
    }
}