<?php


namespace sinri\ark\StaticDocs\handler;


use Parsedown;

/**
 * Class DocumentViewHandler
 * @package sinri\ark\StaticDocs\handler
 * @version 0.1.0
 */
class DocumentViewHandler
{
    /**
     * @var Parsedown
     */
    protected $parseDownInstance;
    /**
     * The source markdown
     * @var string
     */
    protected $markdown;
    /**
     * @var string[]
     */
    protected $components;

    /**
     * @var string
     */
    protected $viewPath;

    public function __construct()
    {
        $this->markdown = '404 Page Not Found';
        $this->components = [];

        $this->viewPath = __DIR__ . '/../view/markdown_page.php';

        $this->parseDownInstance = new Parsedown();
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
     * @return DocumentViewHandler
     */
    public function setViewPath(string $viewPath): DocumentViewHandler
    {
        $this->viewPath = $viewPath;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * @param string[] $components
     * @return DocumentViewHandler
     */
    public function setComponents(array $components): DocumentViewHandler
    {
        $this->components = $components;
        return $this;
    }

    /**
     * The header logo div HTML
     * Override this method to customize
     * @return string
     */
    public function getLogoDiv(): string
    {
        return 'Ark Static Docs - Reader';
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

    /**
     * The target link in footer catalogue link <a/>
     * @param bool $withFromDoc If the target link to return should append from_doc query string
     * @return string
     */
    final public function getCatalogueLink($withFromDoc = false): string
    {
        $link = $this->getRelativeWebPath() . '/catalogue';
        if ($withFromDoc) {
            $link .= '?from_doc=./read/' . implode('/', $this->components);
        }
        return $link;
    }

    final public function getRelativeWebPath(): string
    {
        $totalComponents = count($this->components);
        if (count($this->components) > 1) {
            $link = '..';
            $link .= str_repeat('/..', $totalComponents - 1);
        } elseif (count($this->components) == 1) {
            $link = '..';
        } else {
            $link = '.';
        }
        return $link;
    }

    /**
     * The title written in header of the page
     * Override this method to customize
     * @return string
     */
    public function getTitle(): string
    {
        if (empty($this->components)) {
            return '404 - Page Not Found';
        }

        $titleComponents = [];
        foreach ($this->components as $component) {
            $titleComponents[] = self::parseNameToTitle($component);
        }
        $titleComponents = array_reverse($titleComponents);
        $titleComponents = implode(' - ', $titleComponents);
        return "{$titleComponents} | Elihu Miner Documents";
    }

    /**
     * @param string $name
     * @return string
     *
     * The rules:
     * (0) names are alike `[A-Za-z0-9.()\[\]%_-]+(\.md)?`;
     * (1) all underline chars (`_`) become space chars (` `);
     */
    public static function parseNameToTitle(string $name): string
    {
        if (preg_match('/^([A-Za-z0-9.()\[\]%_-]+)\.md$/', $name, $matches)) {
            $rawName = $matches[1];
            return str_replace('_', ' ', $rawName);
        } elseif (preg_match('/^([A-Za-z0-9.()\[\]%_-]+)$/', $name, $matches)) {
            $rawName = $matches[1];
            return str_replace('_', ' ', $rawName);
        } else {
            return str_replace('/[^A-Za-z0-9.()\[\]%-]+/', ' ', $name);
        }
    }

    /**
     * The content in HTML from the target Markdown
     * @return string
     */
    public function getParsedHtmlOfMarkdown(): string
    {
        return $this->parseDownInstance->text($this->getMarkdown());
    }

    /**
     * the raw content of the target Markdown
     * @return string
     */
    public function getMarkdown(): string
    {
        return $this->markdown;
    }

    /**
     * @param string $markdown
     * @return DocumentViewHandler
     */
    public function setMarkdown(string $markdown): DocumentViewHandler
    {
        $this->markdown = $markdown;
        return $this;
    }

    final public function handleFile(string $realPath, array $components)
    {
        $this->setComponents($components);
        $this->setMarkdown(file_get_contents($realPath));

        Ark()->webOutput()->displayPage(
            $this->viewPath,
            [
                'viewHandler' => $this,
            ]
        );
    }

    /**
     * The breadcrumb div HTML in header
     * @return string
     */
    public function computeBreadcrumbDiv(): string
    {
        $links = [];

        $tailChar = '‣';// '→';

        if (count($this->components) > 1) {
            $x = count($this->components) - 1;
            $y = [];
            for ($j = 0; $j < $x; $j++) {
                $y[] = '..';
            }

            $href = implode('/', $y) . '/';
            $links[] = ['href' => $href, 'title' => 'Home', 'tail' => $tailChar];
            array_shift($y);

            for ($i = 0; $i < count($this->components) - 2; $i++) {
                $href = implode('/', $y) . '/';
                $links[] = ['href' => $href, 'title' => self::parseNameToTitle($this->components[$i]), 'tail' => $tailChar];
                array_shift($y);
            }
            $links[] = ['href' => './', 'title' => self::parseNameToTitle($this->components[count($this->components) - 2]), 'tail' => $tailChar];
            $links[] = ['href' => './' . $this->components[count($this->components) - 1], 'title' => self::parseNameToTitle($this->components[count($this->components) - 1]), 'tail' => ''];
        } elseif (count($this->components) == 1) {
            $links[] = ['href' => './', 'title' => 'Home', 'tail' => $tailChar];
            $links[] = ['href' => './' . $this->components[count($this->components) - 1], 'title' => self::parseNameToTitle($this->components[count($this->components) - 1]), 'tail' => ''];
        } else {
            $links[] = ['href' => './' . $this->components[count($this->components) - 1], 'title' => self::parseNameToTitle($this->components[count($this->components) - 1]), 'tail' => ''];
        }

        $s = '';
        foreach ($links as $link) {
            $s .= "<a href='{$link['href']}'>{$link['title']}</a> {$link['tail']} ";
        }
        return $s;
    }
}