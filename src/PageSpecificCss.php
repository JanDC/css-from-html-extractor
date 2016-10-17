<?php

namespace PageSpecificCss;

use TijsVerkoyen\CssToInlineStyles\Css\Processor;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class PageSpecificCss extends CssToInlineStyles
{

    public function extractCss($page)
    {
        $processor = new Processor();
        return $processor->getRules($processor->getCssFromStyleTags($page));
    }
}