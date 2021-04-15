<?php


namespace sinri\ArkStaticDocs;


use sinri\ark\web\implement\ArkRouteErrorHandlerAsCallback;
use sinri\ark\web\implement\ArkRouteErrorHandlerAsPage;

class PageErrorHandler extends ArkRouteErrorHandlerAsCallback
{
    public function requestErrorCallback($errorMessage, $httpCode)
    {


        echo "<h1>Error</h1>";
        if(is_array($errorMessage)){
            foreach ($errorMessage as $item){
                echo "<p>{$item}</p>";
            }
        }else{
            echo "<p>{$errorMessage}</p>";
        }
    }
}