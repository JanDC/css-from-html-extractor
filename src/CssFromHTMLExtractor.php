<?php

namespace CSSFromHTMLExtractor;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use DOMNodeList;
use Exception;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\CssSelector\Exception\ExceptionInterface;
use CSSFromHTMLExtractor\Css\Processor;
use CSSFromHTMLExtractor\Css\Rule\Rule;
use Symfony\Component\CssSelector\Exception\ExpressionErrorException;

class CssFromHTMLExtractor
{

    /** @var CssSelectorConverter */
    protected $cssConverter;

    /** @var CssStore */
    private $cssStore;

    /** @var Processor */
    private $processor;

    /** @var Rule[] */
    private $rules = [];

    /** @var HtmlStore */
    private $htmlStore;

    /** @var Cache */
    private $resultCache;

    /**
     * CssFromHTMLExtractor constructor.
     * @param Cache|null $resultCache
     */
    public function __construct(Cache $resultCache = null)
    {
        if (class_exists('Symfony\Component\CssSelector\CssSelectorConverter')) {
            $this->cssConverter = new CssSelectorConverter();
        }

        $this->cssStore = new CssStore();
        $this->htmlStore = new HtmlStore();
        $this->processor = new Processor();
        $this->cssConverter = new CssSelectorConverter();

        $this->resultCache = is_null($resultCache) ? new ArrayCache() : $resultCache;
    }

    public function getCssStore()
    {
        return $this->cssStore;
    }

    public function getHtmlStore()
    {
        return $this->htmlStore;
    }

    /**
     * @param string $sourceCss
     */
    public function addBaseRules($sourceCss)
    {
        $identifier = md5($sourceCss);
        if ($this->resultCache->contains($identifier)) {
            list($rules, $charset) = $this->resultCache->fetch($identifier);
            $this->rules = $rules;
            $this->getCssStore()->setCharset($charset);

            return;
        }

        $results = [$this->processor->getRules($sourceCss, $this->rules), $this->processor->getCharset($sourceCss)];

        $this->rules = $results[0];
        $this->getCssStore()->setCharset($results[1]);

        $this->resultCache->save($identifier, $results);
    }

    public function buildExtractedRuleSet()
    {
        $this->processHtmlToStore(implode('', $this->htmlStore->getSnippets()));

        return $this->cssStore->compileStyles();
    }

    /**
     * @param string $html the raw html
     */
    public function processHtmlToStore($html)
    {
        $this->cssStore->addCssStyles($this->extractCss($html));
    }

    /**
     * @param string $html
     *
     * @return \DOMDocument
     */
    protected function createDomDocumentFromHtml($html)
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        if (!empty($html)) {
            $document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        }
        libxml_use_internal_errors($internalErrors);
        $document->formatOutput = true;

        return $document;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function extractCss($html)
    {
        $document = $this->createDomDocumentFromHtml($html);

        $xPath = new \DOMXPath($document);


        $applicable_rules = array_filter(
            $this->rules,
            function (Rule $rule) use ($xPath) {

                try {
                    $expression = $this->cssConverter->toXPath($rule->getSelector());
                } catch (ExpressionErrorException $expressionErrorException) {

                    // Allow for pseudo selectors
                    // TODO: Find a way to validate this exception without checking strings
                    if ($expressionErrorException->getMessage() !== 'Pseudo-elements are not supported.') {
                        return false;
                    }

                    try {
                        $tokens = explode(':', $rule->getSelector());
                        $expression = $this->cssConverter->toXPath((string)reset($tokens));
                    } catch (Exception $e) {
                        return false;
                    }

                } catch (ExceptionInterface $e) {
                    return false;
                }

                /** @var DOMNodeList $elements */
                $elements = $xPath->query($expression);

                if ($elements->length === 0) {
                    return false;
                }

                return true;
            }
        );


        return $applicable_rules;
    }

    public function addHtmlToStore($rawHtml)
    {
        $this->htmlStore->addHtmlSnippet($rawHtml);
    }

    /**
     * @return $this
     */
    public function purgeHtmlStore()
    {
        $this->htmlStore->purge();

        return $this;
    }

    /**
     * @return $this
     */
    public function purgeCssStore()
    {
        $this->cssStore->purge();

        return $this;
    }
}
