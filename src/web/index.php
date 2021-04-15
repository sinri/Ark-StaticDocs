<?php

use sinri\ark\core\ArkHelper;
use sinri\ark\web\implement\ArkRouteErrorHandlerAsJson;
use sinri\ark\web\implement\ArkRouteErrorHandlerAsPage;
use sinri\ArkStaticDocs\CatalogueViewHandler;
use sinri\ArkStaticDocs\DocumentViewHandler;
use sinri\ArkStaticDocs\PageErrorHandler;

require_once __DIR__ . '/../../vendor/autoload.php';

ArkHelper::registerErrorHandlerForLogging(Ark()->logger('WebError'));

$webService = Ark()->webService();
$webService->setLogger(Ark()->logger("web"));

$router = $webService->getRouter();
$router->setLogger(Ark()->logger("router"));

$router->setErrorHandler(new PageErrorHandler());

// todo: auto index

$docRootPath=__DIR__ . '/../../docs';

$webService->setupFileSystemViewer(
    'read',
    $docRootPath,
    [],
    [DocumentViewHandler::class,'handleFile'],
    function ($realPath, $components){
        if (file_exists($realPath . DIRECTORY_SEPARATOR . 'index.md')) {
            Ark()->webOutput()->redirect('./index.md');
        } else {
            Ark()->webOutput()->sendHTTPCode(404);
        }
    }
);

$webService->getRouter()->get('catalogue',function () use ($docRootPath) {
   $handler=new CatalogueViewHandler('Catalogue',$docRootPath);
   Ark()->webOutput()->displayPage(__DIR__.'/../view/catalogue_page.php',['viewHandler'=>$handler]);
});

$webService->handleRequestForWeb();