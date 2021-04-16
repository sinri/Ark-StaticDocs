<?php

use sinri\ark\core\ArkHelper;
use sinri\ark\StaticDocs\ArkStaticDocsService;
use sinri\ark\StaticDocs\handler\CatalogueViewHandler;
use sinri\ark\StaticDocs\handler\DocumentViewHandler;
use sinri\ark\StaticDocs\handler\PageErrorHandler;

require_once __DIR__ . '/../../vendor/autoload.php';

ArkHelper::registerErrorHandlerForLogging(Ark()->logger('WebError'));

// The documents directory
$docRootPath = __DIR__ . '/../docs';
// The handler for error page
$pageErrorHandler = (new PageErrorHandler());
// The handler for document page (in markdown format)
$documentViewHandler = (new DocumentViewHandler());
// The handler for catalogue page
$catalogueViewHandler = (new CatalogueViewHandler());

$staticDocsService = new ArkStaticDocsService(
    Ark()->webService(),
    $docRootPath,
    $pageErrorHandler,
    $documentViewHandler,
    $catalogueViewHandler
);
$staticDocsService->run();