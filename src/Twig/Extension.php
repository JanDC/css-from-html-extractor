<?php

namespace CSSFromHTMLExtractor\Twig;

use CSSFromHTMLExtractor\CssFromHTMLExtractor;
use CSSFromHTMLExtractor\Twig\TokenParsers\FoldTokenParser;
use Twig_Extension;
use Twig_ExtensionInterface;

class Extension extends Twig_Extension implements Twig_ExtensionInterface
{

    /** @var CssFromHTMLExtractor */
    private $pageSpecificCssService;

    /**
     * Extension constructor.
     */
    public function __construct()
    {
        $this->pageSpecificCssService = new CssFromHTMLExtractor();
    }

    /**
     * @param $sourceCss
     */
    public function addBaseRules($sourceCss)
    {
        $this->pageSpecificCssService->addBaseRules($sourceCss);
    }

    public function getTokenParsers()
    {
        return [
            new FoldTokenParser(),
        ];
    }

    public function addCssToExtract($rawHtml)
    {
        $this->pageSpecificCssService->addHtmlToStore($rawHtml);
        return $rawHtml;
    }

    public function getCriticalCss()
    {
        return $this->pageSpecificCssService->getCssStore()->compileStyles();
    }

    public function buildCriticalCssFromSnippets()
    {
        return $this->pageSpecificCssService->buildExtractedRuleSet();
    }
}