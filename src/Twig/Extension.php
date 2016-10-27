<?php

namespace PageSpecificCss\Twig;

use PageSpecificCss\PageSpecificCss;
use PageSpecificCss\Twig\TokenParsers\FoldTokenParser;
use Twig_Extension;
use Twig_ExtensionInterface;

class Extension extends Twig_Extension implements Twig_ExtensionInterface
{

    /** @var PageSpecificCss */
    private $pageSpecificCssService;

    /**
     * Extension constructor.
     *
     * @param string $sourceCss
     */
    public function __construct($sourceCss)
    {
        $this->pageSpecificCssService = new PageSpecificCss($sourceCss);
    }

    public function getTokenParsers()
    {
        return [
            new FoldTokenParser(),
        ];
    }

    public function addCssToExtract($rawHtml)
    {
        $this->pageSpecificCssService->processHtmlToStore($rawHtml);
        return $rawHtml;
    }

    public function getCriticalCss()
    {
        return $this->pageSpecificCssService->getStore()->compileStyles();
    }
}