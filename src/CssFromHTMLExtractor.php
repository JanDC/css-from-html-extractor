<?php

namespace CSSFromHTMLExtractor;

use DOMNodeList;
use Exception;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\CssSelector\Exception\ExceptionInterface;
use CSSFromHTMLExtractor\Css\Processor;
use CSSFromHTMLExtractor\Css\Rule\Rule;
use Symfony\Component\CssSelector\Exception\ExpressionErrorException;

class CssFromHTMLExtractor
{

    /** @var  CssSelectorConverter */
    protected $cssConverter;

    /** @var CssStore */
    private $cssStore;

    /** @var Processor */
    private $processor;

    /** @var Rule[] */
    private $rules = [];

    /** @var HtmlStore */
    private $htmlStore;

    /**
     * CssFromHTMLExtractor constructor.
     */
    public function __construct()
    {
        if (class_exists('Symfony\Component\CssSelector\CssSelectorConverter')) {
            $this->cssConverter = new CssSelectorConverter();
        }

        $this->cssStore = new CssStore();
        $this->htmlStore = new HtmlStore();
        $this->processor = new Processor();
        $this->cssConverter = new CssSelectorConverter();
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
     * @param $sourceCss
     */
    public function addBaseRules($sourceCss)
    {
        $this->rules = $this->processor->getRules($sourceCss, $this->rules);
        $this->getCssStore()->setCharset($this->processor->getCharset($sourceCss));
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
     * @return \DOMDocument
     */
    protected function createDomDocumentFromHtml($html)
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_use_internal_errors($internalErrors);
        $document->formatOutput = true;

        return $document;
    }

    /**
     * @param $html
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

                if ($elements === false || $elements->length == 0) {
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
}