<?php


namespace sinri\ArkStaticDocs;


use sinri\ark\core\ArkFSKit;
use sinri\ark\core\Exception\NotADirectoryException;
use sinri\ark\io\ArkWebInput;

class CatalogueViewHandler
{
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $docRootPath;
    /**
     * @var bool
     */
    protected $isFromDoc;

    public function __construct(string $title, string $docRootPath)
    {
        $this->title = $title;
        $this->docRootPath = $docRootPath;
        $this->isFromDoc=ArkWebInput::getSharedInstance()->readGet('from_doc',false)!==false;
    }

    /**
     * @return bool
     */
    public function isFromDoc(): bool
    {
        return $this->isFromDoc;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return CatalogueViewHandler
     */
    public function setTitle(string $title): CatalogueViewHandler
    {
        $this->title = $title;
        return $this;
    }

    public function getLogoDiv(): string
    {
        return 'ArkStaticDocs Catalogue';
    }

    public function getFooterDiv(): string
    {
        return 'Copyright Sinri Edogawa & Leqee 2021';
    }

    public function getCatalogueDiv(): string
    {
        $r=$this->dumpDocTree($this->docRootPath);
        $s='<div>';
        $s.=$this->parseDocTreeNodeToHtmlDiv($r,0);
        $s.="</div>";

        return $s;
    }

    /**
     * @param string $root
     * @param string $parentPath
     * @return array
     * @throws NotADirectoryException
     */
    protected function dumpDocTree(string $root, string $parentPath=''):array{
        $name=basename($root);
        $title=DocumentViewHandler::parseNameToTitle($name);
        if(is_dir($root)){
            if($parentPath===''){
                $parentPath='./read/';
                $r= [
                    'name'=>$name,
                    'title'=>'Home',
                    'href'=>$parentPath,
                ];
            }else {
                $parentPath=$parentPath.$name.'/';
                $r= [
                    'name' => $name,
                    'title' => $title,
                    'href' => $parentPath,
                ];
            }

            // children
            $children=[];
            ArkFSKit::walkThroughItemsInDir($root,function(string $item,string $dir)use($parentPath, &$children){
                $itemFullPath=$dir.DIRECTORY_SEPARATOR.$item;
                $name=basename($itemFullPath);
                $title=DocumentViewHandler::parseNameToTitle($name);
                if(is_file($itemFullPath)){
                    $children[]=[
                        'name' => $name,
                        'title' => $title,
                        'href' => $parentPath.$name,
                    ];
                }else{
                    $children[]=$this->dumpDocTree($itemFullPath,$parentPath);
                }
            });

            $r['children']=$children;

            return $r;
        }else{
            if($parentPath==='') {
                throw new NotADirectoryException("[{$root}] is not a directory for doc root.");
            }
            return [
                'name' => $name,
                'title' => $title,
                'href' => $parentPath.$name,
            ];
        }
    }

    protected function parseDocTreeNodeToHtmlDiv(array $node,int $depth):string{
        $is_dir=false;
        if(isset($node['children']) && is_array($node['children'])){
            $is_dir=true;
        }

        $s="<div style='margin: auto'>";
        $s .= str_repeat("<div style='display: inline-block;width: 20px;color: lightgrey;border-left: 1px solid lightgrey;'>&nbsp;</div>", $depth);
//        if($depth>0) {
//            $s .= "<div style='display: inline-block;width: 20px;color: lightgrey;'>➤</div>";
//        }

        $s.="<div style='display: inline-block;'>";
        if($is_dir){
            $s.='<span>📁&nbsp;</span>';
        }else{
            $s.='<span>📄&nbsp;</span>';
        }
        if($this->isFromDoc){
            $s .= "<a href='{$node['href']}' target='_parent'>{$node['title']}</a>";
        }else {
            $s .= "<a href='{$node['href']}'>{$node['title']}</a>";
        }
        $s.="</div>";
        if($is_dir){
            $s.="<div>";
            foreach ($node['children'] as $child){
                $s.=$this->parseDocTreeNodeToHtmlDiv($child,$depth+1);
            }
            $s.="</div>";
        }
        $s.="</div>";
        return $s;
    }
}