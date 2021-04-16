<?php

use sinri\ark\core\ArkHelper;
use sinri\ark\StaticDocs\ArkStaticDocsService;
use sinri\ark\StaticDocs\handler\CatalogueViewHandler;
use sinri\ark\StaticDocs\handler\DocumentViewHandler;
use sinri\ark\StaticDocs\handler\PageErrorHandler;

require_once __DIR__ . '/../../vendor/autoload.php';

ArkHelper::registerErrorHandlerForLogging(Ark()->logger('WebError'));

$staticDocsService = new ArkStaticDocsService(
    Ark()->webService(),
    __DIR__ . '/../docs',
    (new PageErrorHandler()),
    (new DocumentViewHandler()),
    (new CatalogueViewHandler())
);
$staticDocsService->run();