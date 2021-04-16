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
 * @version 0.1.0
 */
class ArkStaticDocsService
{
    /**
     * @var ArkWebService
     */
    protected $arkWebService;
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

    public function __construct(
        ArkWebService $arkWebService,
        string $docRootPath,
        PageErrorHandler $pageErrorHandler,
        DocumentViewHandler $documentViewHandler,
        CatalogueViewHandler $catalogueViewHandler
    )
    {
        $this->arkWebService = $arkWebService;
        $this->docRootPath = $docRootPath;
        $this->pageErrorHandler = $pageErrorHandler;
        $this->documentViewHandler = $documentViewHandler;
        $this->catalogueViewHandler = $catalogueViewHandler;

        $this->catalogueViewHandler->setDocRootPath($this->docRootPath);
    }

    public function run()
    {
        $router = $this->arkWebService->getRouter();
        $router->setErrorHandler(new PageErrorHandler());

        $docRootPath = $this->docRootPath;

        $this->arkWebService->setupFileSystemViewer(
            'read',
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
            'catalogue',
            [$this->catalogueViewHandler, 'handle']
        );

        $this->arkWebService->handleRequestForWeb();
    }

}