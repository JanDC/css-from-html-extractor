<?php

namespace PageSpecificCss;

use TijsVerkoyen\CssToInlineStyles\Css\Processor;
use TijsVerkoyen\CssToInlineStyles\Css\Rule\Rule;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class PageSpecificCss extends CssToInlineStyles
{

    /**
     * @var CssStore
     */
    private $cssStore;

    /**
     * PageSpecificCss constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->cssStore = new CssStore();

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
        // Do something..
        return '';
    }
}