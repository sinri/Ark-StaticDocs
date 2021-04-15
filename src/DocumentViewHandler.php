<?php


namespace sinri\ArkStaticDocs;


use Parsedown;

class DocumentViewHandler
{
    /**
     * @var Parsedown
     */
    protected $parseDownInstance;
    /**
     * @var string
     */
    protected $title;
    /**
     * The source markdown
     * @var string
     */
    protected $markdown;
    /**
     * @var string[]
     */
    protected $components;

    public function __construct(string $title, string $markdown, array $components)
    {
        $this->title = $title;
        $this->markdown = $markdown;
        $this->components = $components;

        $this->parseDownInstance = new Parsedown();
    }

    public function getLogoDiv(): string
    {
        return 'ArkStaticDocs';
    }

    public function getFooterDiv(): string
    {
        return 'Copyright Sinri Edogawa & Leqee 2021';
    }

    public function getRelativeWebPath():string{
        $totalComponents=count($this->components);
        if (count($this->components) > 1) {
            $link='..';
            $link .= str_repeat('/..', $totalComponents - 1);
        }elseif(count($this->components)==1) {
            $link='..';
        }else{
            $link='.';
        }
        return $link;
    }

    public function getCatalogueLink($withFromDoc=false): string
    {
//        $totalComponents=count($this->components);
//        if (count($this->components) > 1) {
//            $link='..';
//            $link .= str_repeat('/..', $totalComponents - 1);
//        }elseif(count($this->components)==1) {
//            $link='..';
//        }else{
//            $link='.';
//        }
//        $link.='/catalogue';
        $link=$this->getRelativeWebPath().'/catalogue';
        if($withFromDoc){
            $link.='?from_doc=./read/'.implode('/',$this->components);
        }
        return $link;
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
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMarkdown(): string
    {
        return $this->markdown;
    }

    /**
     * @param string $markdown
     */
    public function setMarkdown(string $markdown)
    {
        $this->markdown = $markdown;
    }

    public function getParsedHtmlOfMarkdown(): string
    {
        return $this->parseDownInstance->text($this->getMarkdown());
    }

    public static function handleFile(string $realPath, array $components)
    {
//        echo json_encode(['type'=>'file','realpath'=>$realPath,'components'=>$components]);

        $title = implode('::', $components);
        $title = "Page [{$title}] of Elihu Miner Documents";

        $md_str = file_get_contents($realPath);

        Ark()->webOutput()->displayPage(
            __DIR__ . '/view/markdown_page.php',
            ['viewHandler' => new DocumentViewHandler($title, $md_str,$components)]
        );
    }

    public function computeBreadcrumbDiv(): string
    {
        $links = [];

        $tailChar='‣';// '→';

        if (count($this->components) > 1) {
            $x = count($this->components) - 1;
            $y = [];
            for ($j = 0; $j < $x; $j++) {
                $y[] = '..';
            }

            $href = implode('/', $y) . '/';
//            echo "<a href='$href'>Home</a> → ";
            $links[] = ['href' => $href, 'title' => 'Home', 'tail' => $tailChar];
            array_shift($y);

            for ($i = 0; $i < count($this->components) - 2; $i++) {
                $href = implode('/', $y) . '/';
//                echo "<a href='{$href}'>{$components[$i]} ({$href})</a> → ";
                $links[] = ['href' => $href, 'title' => self::parseNameToTitle($this->components[$i]), 'tail' => $tailChar];
                array_shift($y);
            }
//            echo "<a href='./'>{$components[count($components)-2]}</a> → ";
            $links[] = ['href' => './', 'title' => self::parseNameToTitle($this->components[count($this->components) - 2]), 'tail' => $tailChar];
//            echo $this->components[count($this->components) - 1];
            $links[] = ['href' => './' . $this->components[count($this->components) - 1], 'title' => self::parseNameToTitle($this->components[count($this->components) - 1]), 'tail' => ''];
        } elseif(count($this->components)==1) {
            $links[] = ['href' => './', 'title' => 'Home', 'tail' => $tailChar];
            $links[] = ['href' => './' . $this->components[count($this->components) - 1], 'title' => self::parseNameToTitle($this->components[count($this->components) - 1]), 'tail' => ''];
        }else{
            $links[] = ['href' => './' . $this->components[count($this->components) - 1], 'title' => self::parseNameToTitle($this->components[count($this->components) - 1]), 'tail' => ''];
        }

        $s = '';
        foreach ($links as $link) {
            $s .= "<a href='{$link['href']}'>{$link['title']}</a> {$link['tail']} ";
        }
        return $s;
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
        if(preg_match('/^([A-Za-z0-9.()\[\]%_-]+)\.md$/',$name,$matches)){
            $rawName=$matches[1];
            return str_replace('_',' ',$rawName);
        }elseif(preg_match('/^([A-Za-z0-9.()\[\]%_-]+)$/',$name,$matches)){
            $rawName=$matches[1];
            return str_replace('_',' ',$rawName);
        }else{
            return str_replace('/[^A-Za-z0-9.()\[\]%-]+/',' ',$name);
        }
    }
}