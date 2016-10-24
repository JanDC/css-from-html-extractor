<?php

namespace PageSpecificCss;

use TijsVerkoyen\CssToInlineStyles\Css\Processor;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class PageSpecificCss extends CssToInlineStyles
{

    /** @var CssStore */
    private $cssStore;

    /** @var Processor */
    private $processor;

    /**
     * PageSpecificCss constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->cssStore = new CssStore();
        $this->processor = new Processor(true);
    }

    /**
     * @param string $html the raw html
     */
    public function processHtmlToStore($html)
    {
        $this->cssStore->addCssStyle($this->extractCss($html));
    }

    /**
     * @param $html
     *
     * @return string
     */
    public function extractCss($html)
    {
        return $this->processor->getCssFromStyleTags($html);
    }
}