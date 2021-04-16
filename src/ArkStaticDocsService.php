<?php


namespace sinri\ark\StaticDocs;


use sinri\ark\io\ArkWebOutput;
use sinri\ark\StaticDocs\handler\CatalogueViewHandler;
use sinri\ark\StaticDocs\handler\DocumentViewHandler;
use sinri\ark\StaticDocs\handler\PageErrorHandler;
use sinri\ark\web\ArkWebService;

/**
 * Class ArkStaticDocsService
 * @package sinri\ark\StaticDocs
 * @version 0.1.1
 */
class ArkStaticDocsService
{
    /**
     * @var ArkWebService
     */
    protected $arkWebService;
    /**
     * @var string
     * @since 0.2.0
     */
    protected $pathPrefix;
    /**
     * @var string
     */
    protected $docRootPath;
    /**
     * @var PageErrorHandler
     */
    protected $pageErrorHandler;
    /**
     * @var DocumentViewHandler
     */
    protected $documentViewHandler;
    /**
     * @var CatalogueViewHandler
     */
    protected $catalogueViewHandler;

    /**
     * ArkStaticDocsService constructor.
     * @param ArkWebService $arkWebService
     * @param string $docRootPath
     * @param string $pathPrefix empty or 'xxx' (without tail `/`) @since 0.2.0
     * @param PageErrorHandler|null $pageErrorHandler
     * @param DocumentViewHandler|null $documentViewHandler
     * @param CatalogueViewHandler|null $catalogueViewHandler
     */
    public function __construct(
        ArkWebService $arkWebService,
        string $docRootPath,
        string $pathPrefix = '',
        ?PageErrorHandler $pageErrorHandler = null,
        ?DocumentViewHandler $documentViewHandler = null,
        ?CatalogueViewHandler $catalogueViewHandler = null
    )
    {
        $this->arkWebService = $arkWebService;

        $this->pathPrefix = $pathPrefix;
        $this->docRootPath = $docRootPath;

        if ($pageErrorHandler === null) $pageErrorHandler = new PageErrorHandler();
        $this->pageErrorHandler = $pageErrorHandler;

        if ($documentViewHandler === null) $documentViewHandler = new DocumentViewHandler();
        $this->documentViewHandler = $documentViewHandler;

        if ($catalogueViewHandler === null) $catalogueViewHandler = new CatalogueViewHandler();
        $this->catalogueViewHandler = $catalogueViewHandler;
        $this->catalogueViewHandler->setDocRootPath($this->docRootPath);
    }

    /**
     * @return $this
     * @since 0.1.1
     * @since 0.2.0 support url path prefix
     */
    public function install(): ArkStaticDocsService
    {
        $router = $this->arkWebService->getRouter();
        $router->setErrorHandler(new PageErrorHandler());

        $docRootPath = $this->docRootPath;

        $pathPrefix = $this->pathPrefix;
        if (preg_match('/[^\/]$/', $pathPrefix)) {
            $pathPrefix .= '/';
        }

        $this->arkWebService->setupFileSystemViewer(
            $pathPrefix . 'read',
            $docRootPath,
            [],
            [$this->documentViewHandler, 'handleFile'],
            function ($realPath, $components) {
                if (file_exists($realPath . DIRECTORY_SEPARATOR . 'index.md')) {
                    ArkWebOutput::getSharedInstance()->redirect('./index.md');
                } else {
                    ArkWebOutput::getSharedInstance()->sendHTTPCode(404);
                }
            }
        );

        $this->arkWebService->getRouter()->get(
            $pathPrefix . 'catalogue',
            [$this->catalogueViewHandler, 'handle']
        );

        return $this;
    }

    /**
     * @since 0.1.1
     */
    public function run()
    {
        $this->arkWebService->handleRequestForWeb();
    }

}