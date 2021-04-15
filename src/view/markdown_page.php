<?php

use sinri\ArkStaticDocs\DocumentViewHandler;

if (!isset($viewHandler)){
    $viewHandler=new DocumentViewHandler('Error','Input Incorrect',[]);
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title><?php echo $viewHandler->getTitle(); ?></title>
        <link rel="stylesheet" href="<?php echo $viewHandler->getRelativeWebPath().'/github-markdown-css/4.0.0/github-markdown.min.css' ?>">
        <style>
            body{
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

            #parsed_md_div {
                margin: 50px 10px 50px 300px;
                padding: 10px;
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
            }
            #catalogue_div{
                position: fixed;
                left:0;
                top:50px;
                bottom: 50px;
                width: 300px;
                border-right: 1px solid gray;
            }
            #catalogue_iframe{
                height: 100%;
                width: 300px;
                border:none;
            }
        </style>
    </head>
    <body>
        <div id="header_div">
            <div style="display: inline-block;"><?php echo $viewHandler->getLogoDiv(); ?></div>
            <div style="display: inline-block;margin-left:50px;font-size: 10px;line-height: 22px"><?php echo $viewHandler->computeBreadcrumbDiv();?></div>
        </div>
        <div id='parsed_md_div' class='markdown-body'>
            <?php echo $viewHandler->getParsedHtmlOfMarkdown(); ?>
        </div>
        <div id="footer_div">
            <div style="display: inline-block;margin: auto 5px;"><?php echo $viewHandler->getFooterDiv(); ?></div>
            <div style="display: inline-block;color: gray;">|</div>
            <div style="display: inline-block;margin: auto 5px;">
                <a href="<?php echo $viewHandler->getCatalogueLink();?>">Catalogue</a>
            </div>
        </div>
        <div id="catalogue_div">
            <iframe id="catalogue_iframe" name="catalogue_iframe"
                    src="<?php echo $viewHandler->getCatalogueLink(true);?>"
            ></iframe>
        </div>
    </body>
</html>