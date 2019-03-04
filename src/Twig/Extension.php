<?php

namespace CSSFromHTMLExtractor\Twig;

use CSSFromHTMLExtractor\CssFromHTMLExtractor;
use CSSFromHTMLExtractor\Twig\TokenParsers\FoldTokenParser;
use Doctrine\Common\Cache\Cache;
use Twig_Extension;
use Twig_ExtensionInterface;

class Extension extends Twig_Extension implements Twig_ExtensionInterface
{

    /** @var CssFromHTMLExtractor */
    private $pageSpecificCssService;

    /**
     * @param Cache|null $resultSetCache
     */
    public function __construct(Cache $resultSetCache = null)
    {
        $this->pageSpecificCssService = new CssFromHTMLExtractor($resultSetCache);
    }

    /**
     * @param string $sourceCss
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

    public function hasCriticalHtml()
    {
        return count($this->pageSpecificCssService->getHtmlStore()->getSnippets()) > 0;
    }

    public function getCriticalCss()
    {
        return $this->pageSpecificCssService->getCssStore()->compileStyles();
    }

    public function buildCriticalCssFromSnippets()
    {
        return $this->pageSpecificCssService->buildExtractedRuleSet();
    }

    public function purgeHtmlStore()
    {
        $this->pageSpecificCssService->purgeHtmlStore();

    }

    public function purgeCssStore()
    {
        $this->pageSpecificCssService->purgeCssStore();
    }
}
