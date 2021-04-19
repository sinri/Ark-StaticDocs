<?php


namespace sinri\ark\StaticDocs\handler;


use sinri\ark\core\ArkFSKit;
use sinri\ark\core\ArkHelper;
use sinri\ark\core\Exception\NotADirectoryException;
use sinri\ark\io\ArkWebInput;
use sinri\ark\io\ArkWebOutput;

/**
 * Class CatalogueViewHandler
 * @package sinri\ark\StaticDocs\handler
 * @version 0.1.0
 */
class CatalogueViewHandler
{
    /**
     * @var string
     */
    protected $docRootPath;
    /**
     * @var bool
     */
    protected $isFromDoc;
    /**
     * String when $isFromDoc is true, null else.
     * @var string|null
     */
    protected $fromDoc;
    /**
     * @var string
     */
    protected $viewPath;
    /**
     * @var bool
     * @since 0.2.3
     */
    protected $hideIndexNode;

    public function __construct()
    {
        $this->docRootPath = '/dev/null';
        $this->fromDoc = ArkWebInput::getSharedInstance()->readGet('from_doc');
        $this->isFromDoc = $this->fromDoc !== null;
        $this->viewPath = __DIR__ . '/../view/catalogue_page.php';
        $this->hideIndexNode = true;
    }

    /**
     * @return bool
     * @since 0.2.3
     */
    public function isHideIndexNode(): bool
    {
        return $this->hideIndexNode;
    }

    /**
     * @param bool $hideIndexNode
     * @return CatalogueViewHandler
     * @since 0.2.3
     */
    public function setHideIndexNode(bool $hideIndexNode): CatalogueViewHandler
    {
        $this->hideIndexNode = $hideIndexNode;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocRootPath(): string
    {
        return $this->docRootPath;
    }

    /**
     * @param string $docRootPath
     * @return CatalogueViewHandler
     */
    public function setDocRootPath(string $docRootPath): CatalogueViewHandler
    {
        $this->docRootPath = $docRootPath;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFromDoc(): bool
    {
        return $this->isFromDoc;
    }

    /**
     * The title written in header of the page
     * Override this method to customize
     * @return string
     */
    public function getTitle(): string
    {
        return 'Catalogue';
    }

    /**
     * The header logo div HTML
     * Override this method to customize
     * @return string
     */
    public function getLogoDiv(): string
    {
        return 'Ark Static Docs - Catalogue';
    }

    /**
     * The footer div HTML
     * Override this method to customize
     * @return string
     */
    public function getFooterDiv(): string
    {
        return 'Copyright Sinri Edogawa 2021. Powered by <a href="https://github.com/sinri/Ark-StaticDocs">Ark Static Docs</a>.';
    }

    final public function getCatalogueDiv(): string
    {
        try {
            $r = $this->dumpDocTree($this->docRootPath);
            $s = '<div>';
            $s .= $this->parseDocTreeNodeToHtmlDiv($r, 0);
            $s .= "</div>";

            return $s;
        } catch (NotADirectoryException $e) {
            return "<div>{$e->getMessage()}</div>";
        }
    }

    /**
     * @param string $root
     * @param string $parentPath
     * @return array
     * @throws NotADirectoryException
     */
    protected function dumpDocTree(string $root, string $parentPath = ''): array
    {
        $name = basename($root);
        $title = DocumentViewHandler::parseNameToTitle($name);
        if (is_dir($root)) {
            if ($parentPath === '') {
                $parentPath = './read/';
                $r = [
                    'name' => $name,
                    'title' => 'Home',
                    'href' => $parentPath,
                    'is_current_page' => false,
                ];
            } else {
                $parentPath = $parentPath . $name . '/';
                $r = [
                    'name' => $name,
                    'title' => $title,
                    'href' => $parentPath,
                    'is_current_page' => false,
                ];
            }

            if ($this->isFromDoc) {
                if (ArkHelper::stringHasSuffix($this->fromDoc, '/index.md')) {
                    if (($r['href'] . 'index.md') === $this->fromDoc) {
                        $r['is_current_page'] = true;
                    }
                } else {
                    if ($r['href'] === $this->fromDoc) {
                        $r['is_current_page'] = true;
                    }
                }
            }

            // children
            $children = [];
            ArkFSKit::walkThroughItemsInDir($root, function (string $item, string $dir) use ($parentPath, &$children) {
                $itemFullPath = $dir . DIRECTORY_SEPARATOR . $item;
                $name = basename($itemFullPath);
                $title = DocumentViewHandler::parseNameToTitle($name);
                if (is_file($itemFullPath)) {
                    if ($this->hideIndexNode && $item === 'index.md') {
                        return;
                    }
                    $r = [
                        'name' => $name,
                        'title' => $title,
                        'href' => $parentPath . $name,
                        'is_current_page' => false,
                    ];

                    if ($this->isFromDoc) {
                        if (ArkHelper::stringHasSuffix($this->fromDoc, '/index.md')) {
                            if (($r['href'] . 'index.md') === $this->fromDoc) {
                                $r['is_current_page'] = true;
                            }
                        } else {
                            if ($r['href'] === $this->fromDoc) {
                                $r['is_current_page'] = true;
                            }
                        }
                    }

                    $children[] = $r;
                } else {
                    $children[] = $this->dumpDocTree($itemFullPath, $parentPath);
                }
            });

            $r['children'] = $children;

            return $r;
        } else {
            if ($parentPath === '') {
                throw new NotADirectoryException("[{$root}] is not a directory for doc root.");
            }
            $r = [
                'name' => $name,
                'title' => $title,
                'href' => $parentPath . $name,
                'is_current_page' => false,
            ];

            if ($this->isFromDoc) {
                if (ArkHelper::stringHasSuffix($this->fromDoc, '/index.md')) {
                    if (($r['href'] . 'index.md') === $this->fromDoc) {
                        $r['is_current_page'] = true;
                    }
                } else {
                    if ($r['href'] === $this->fromDoc) {
                        $r['is_current_page'] = true;
                    }
                }
            }

            return $r;
        }
    }

    protected function parseDocTreeNodeToHtmlDiv(array $node, int $depth): string
    {
        $is_dir = false;
        if (isset($node['children']) && is_array($node['children'])) {
            $is_dir = true;
        }

        $s = "<div style='margin: auto'>";
        $s .= str_repeat("<div style='display: inline-block;width: 20px;color: lightgrey;border-left: 1px solid lightgrey;'>&nbsp;</div>", $depth);

        $s .= "<div style='display: inline-block;'>";
        if ($is_dir) {
            $s .= '<span>📁&nbsp;</span>';
        } else {
            $s .= '<span>📄&nbsp;</span>';
        }
        if ($this->isFromDoc) {
            $s .= "<a href='{$node['href']}' target='_parent'>{$node['title']}</a>";
        } else {
            $s .= "<a href='{$node['href']}'>{$node['title']}</a>";
        }
        if ($node['is_current_page']) {
            $s .= "&nbsp;<span style='color: gray;'>←</span>";
        }
        $s .= "</div>";
        if ($is_dir) {
            $s .= "<div>";
            foreach ($node['children'] as $child) {
                $s .= $this->parseDocTreeNodeToHtmlDiv($child, $depth + 1);
            }
            $s .= "</div>";
        }
        $s .= "</div>";
        return $s;
    }

    final public function handle()
    {
        ArkWebOutput::getSharedInstance()->displayPage(
            $this->getViewPath(),
            [
                'viewHandler' => $this,
            ]
        );
    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * @param string $viewPath
     * @return CatalogueViewHandler
     */
    public function setViewPath(string $viewPath): CatalogueViewHandler
    {
        $this->viewPath = $viewPath;
        return $this;
    }
}