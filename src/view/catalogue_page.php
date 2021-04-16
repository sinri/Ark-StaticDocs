<?php

use sinri\ark\StaticDocs\handler\CatalogueViewHandler;

if (!isset($viewHandler)){
    $viewHandler = (new CatalogueViewHandler)->setTitle('Unknown')->setDocRootPath('/tmp/no/such/path');
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title><?php echo $viewHandler->getTitle(); ?></title>
    <!--suppress HtmlUnknownTarget -->
    <link rel="stylesheet" href="./static/github-markdown-css/4.0.0/github-markdown.min.css">
    <style>
        body {
            margin: 0;
            background: white;
        }

        #header_div {
            background-color: #dddddd;
            padding: 10px;
            height: 30px;
            position: fixed;
            top:0;
            width: 100%;
            line-height: 30px;
            <?php if($viewHandler->isFromDoc()){ ?>
            display: none;
            <?php } ?>
        }
        #header_div a:link{
            text-decoration: none;
            color: gray;
        }
        #header_div a:visited{
            text-decoration: none;
            color: gray;
        }
        #header_div a:hover{
            text-decoration: none;
            color: cornflowerblue;
        }
        #catalogue_div {
            margin: <?php if(!$viewHandler->isFromDoc()){ ?> 50px 10px 50px <?php } else { ?> 10px <?php } ?>;
        }
        #footer_div{
            background-color: #dddddd;
            text-align: center;
            padding: 10px;
            height: 30px;
            width: 100%;
            position: fixed;
            bottom: 0;
            line-height: 30px;
            <?php if($viewHandler->isFromDoc()){ ?>
            display: none;
            <?php } ?>
        }
    </style>
</head>
<body>
    <div id="header_div">
        <div style="display: inline-block;"><?php echo $viewHandler->getLogoDiv(); ?></div>
    </div>
    <div id='catalogue_div' class='markdown-body'>
        <?php echo $viewHandler->getCatalogueDiv(); ?>
    </div>
    <div id="footer_div">
        <?php echo $viewHandler->getFooterDiv(); ?>
    </div>
    <!--suppress JSUnusedGlobalSymbols -->
    <script lang="JavaScript">
        function locateParentToTargetPage(target) {
            window.parent.window.location = target;
        }
    </script>
</body>
</html>
