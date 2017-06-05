<?php

namespace Spiral\Pages\Controllers;

use Spiral\Core\Controller;
use Spiral\Http\Exceptions\ClientExceptions\ForbiddenException;
use Spiral\Pages\Config;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\EditorInterface;
use Spiral\Pages\Permissions;
use Spiral\Pages\Requests\Api\MetaRequest;
use Spiral\Pages\Requests\Api\SourceRequest;
use Spiral\Pages\Services\PageEditor;
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

    private $source;
    private $config;
    private $permissions;

    /**
     * @param PageSource  $source
     * @param Config      $config
     * @param Permissions $permissions
     */
    public function __construct(PageSource $source, Config $config, Permissions $permissions)
    {
        $this->source = $source;
        $this->config = $config;
        $this->permissions = $permissions;
    }

    /**
     * @param string|int $id
     *
     * @return array
     */
    public function getMetaAction(string $id): array
    {
        $this->allows($this->config->editCMSPermission());

        $page = $this->source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Unable to find requested page')
            ];
        }

        if ($page->status->isDraft() && !$this->permissions->canViewDraft()) {
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
     * @param MetaRequest     $request
     * @param PageEditor      $service
     * @param EditorInterface $editor
     *
     * @return array
     */
    public function saveMetaAction(
        string $id,
        MetaRequest $request,
        PageEditor $service,
        EditorInterface $editor
    ): array {
        $this->allows($this->config->editCMSPermission());

        $page = $this->source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Unable to find requested page')
            ];
        }

        if ($page->status->isDraft() && !$this->permissions->canViewDraft()) {
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
     * @param string|int $id
     *
     * @return array
     */
    public function getSourceAction(string $id): array
    {
        $this->allows($this->config->editCMSPermission());

        $page = $this->source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Unable to find requested page')
            ];
        }

        if ($page->status->isDraft() && !$this->permissions->canViewDraft()) {
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
     * @param SourceRequest   $request
     * @param PageEditor      $service
     * @param EditorInterface $editor
     *
     * @return array
     */
    public function saveSourceAction(
        string $id,
        SourceRequest $request,
        PageEditor $service,
        EditorInterface $editor
    ) {
        $this->allows($this->config->editCMSPermission());

        $page = $this->source->findByPK($id);
        if (empty($page)) {
            return [
                'status' => 400,
                'error'  => $this->say('Unable to find requested page')
            ];
        }

        if ($page->status->isDraft() && !$this->permissions->canViewDraft()) {
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
