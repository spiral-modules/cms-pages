<?php

namespace Spiral\Pages\Controllers;

use Spiral\Core\Controller;
use Spiral\Http\Exceptions\ClientExceptions\ForbiddenException;
use Spiral\Pages\Config;
use Spiral\Pages\EditorInterface;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Requests\Api\MetaRequest;
use Spiral\Pages\Requests\Api\SourceRequest;
use Spiral\Pages\Services\PageEditor;
use Spiral\Pages\Permissions;
use Spiral\Security\Traits\GuardedTrait;
use Spiral\Translator\Traits\TranslatorTrait;

/**
 * Class ApiController
 *
 * @package Spiral\Pages\Controllers
 */
class ApiController extends Controller
{
    use GuardedTrait, TranslatorTrait;

    /**
     * @param string|int  $id
     * @param PageSource  $source
     * @param Config      $config
     * @param Permissions $permissions
     * @return array
     */
    public function getMetaAction($id, PageSource $source, Config $config, Permissions $permissions)
    {
        $this->allows($config->editCMSPermission());

        $page = $source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Unable to find requested page')
            ];
        }

        if ($page->status->isDraft() && !$permissions->canViewDraft()) {
            throw new ForbiddenException();
        }

        return [
            'status' => 200,
            'page'   => [
                'html'        => $page->metaTags,
                'title'       => $page->title,
                'description' => $page->description,
                'keywords'    => $page->keywords
            ]
        ];
    }

    /**
     * @param string|int      $id
     * @param PageSource      $source
     * @param MetaRequest     $request
     * @param Config          $config
     * @param PageEditor      $service
     * @param EditorInterface $editor
     * @param Permissions     $permissions
     * @return array
     */
    public function saveMetaAction(
        $id,
        PageSource $source,
        MetaRequest $request,
        Config $config,
        PageEditor $service,
        EditorInterface $editor,
        Permissions $permissions
    ) {
        $this->allows($config->editCMSPermission());

        $page = $source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Unable to find requested page')
            ];
        }

        if ($page->status->isDraft() && !$permissions->canViewDraft()) {
            throw new ForbiddenException();
        }

        if (!$request->isValid()) {
            return [
                'status' => 400,
                'errors' => $request->getErrors()
            ];
        }

        $service->setFields($page, $request, $editor);
        $page->save();

        return [
            'status'  => 200,
            'message' => $this->say('Page meta updated.')
        ];
    }

    /**
     * @param string|int  $id
     * @param PageSource  $source
     * @param Config      $config
     * @param Permissions $permissions
     * @return array
     */
    public function getSourceAction(
        $id,
        PageSource $source,
        Config $config,
        Permissions $permissions
    ) {
        $this->allows($config->editCMSPermission());

        $page = $source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Unable to find requested page')
            ];
        }

        if ($page->status->isDraft() && !$permissions->canViewDraft()) {
            throw new ForbiddenException();
        }

        return [
            'status' => 200,
            'page'   => [
                'source' => $page->source,
            ]
        ];
    }

    /**
     * @param string|int      $id
     * @param PageSource      $source
     * @param SourceRequest   $request
     * @param Config          $config
     * @param PageEditor      $service
     * @param Permissions     $permissions
     * @param EditorInterface $editor
     * @return array
     */
    public function saveSourceAction(
        $id,
        PageSource $source,
        SourceRequest $request,
        Config $config,
        PageEditor $service,
        Permissions $permissions,
        EditorInterface $editor
    ) {
        $this->allows($config->editCMSPermission());

        $page = $source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Unable to find requested page')
            ];
        }

        if ($page->status->isDraft() && !$permissions->canViewDraft()) {
            throw new ForbiddenException();
        }

        if (!$request->isValid()) {
            return [
                'status' => 400,
                'errors' => $request->getErrors()
            ];
        }

        $service->setFields($page, $request, $editor);
        $page->save();

        return [
            'status'  => 200,
            'message' => $this->say('Page source updated.')
        ];
    }
}