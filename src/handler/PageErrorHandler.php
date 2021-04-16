<?php


namespace sinri\ark\StaticDocs\handler;


use sinri\ark\web\implement\ArkRouteErrorHandlerAsCallback;

/**
 * Class PageErrorHandler
 * @package sinri\ark\StaticDocs\handler
 * @version 0.1.0
 */
class PageErrorHandler extends ArkRouteErrorHandlerAsCallback
{
    /**
     * Override this to customize
     * @param array $errorMessage
     * @param int $httpCode
     */
    public function requestErrorCallback($errorMessage, $httpCode)
    {
        echo "<h1>Error</h1>";
        if (is_array($errorMessage)) {
            foreach ($errorMessage as $item) {
                echo "<p>{$item}</p>";
            }
        } else {
            echo "<p>{$errorMessage}</p>";
        }
    }
}