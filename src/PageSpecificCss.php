<?php

namespace PageSpecificCss;

use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\CssSelector\Exception\ExceptionInterface;
use TijsVerkoyen\CssToInlineStyles\Css\Processor;
use TijsVerkoyen\CssToInlineStyles\Css\Rule\Processor as RuleProcessor;
use TijsVerkoyen\CssToInlineStyles\Css\Rule\Rule;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class PageSpecificCss extends CssToInlineStyles
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
     * PageSpecificCss constructor.
     */
    public function __construct()
    {
        parent::__construct();

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
    }

    public function buildExtractedRuleSet()
    {
        foreach ($this->htmlStore->getSnippets() as $htmlSnippet) {
            $this->processHtmlToStore($htmlSnippet);
        }

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
     * @param $html
     *
     * @return string
     */
    public function extractCss($html)
    {
        $document = $this->createDomDocumentFromHtml($html);

        $xPath = new \DOMXPath($document);

        usort($this->rules, [RuleProcessor::class, 'sortOnSpecificity']);

        $applicable_rules = array_filter($this->rules, function (Rule $rule) use ($xPath) {
            try {
                $expression = $this->cssConverter->toXPath($rule->getSelector());
            } catch (ExceptionInterface $e) {
                return false;
            }

            $elements = $xPath->query($expression);

            if ($elements === false || $elements->length == 0) {
                return false;
            }

            return true;
        });

        $applicable_rules = $this->groupRulesBySelector($applicable_rules);
        return $applicable_rules;
    }

    /**
     * @param Rule[] $applicable_rules
     *
     * @return  array
     */
    private function groupRulesBySelector($applicable_rules)
    {
        $grouped = [];

        foreach ($applicable_rules as $applicable_rule) {
            /** @var Rule $applicable_rule */
            if (isset($grouped[$applicable_rule->getSelector()])) {
                $grouped[$applicable_rule->getSelector()] = array_merge($grouped[$applicable_rule->getSelector()], $applicable_rule->getProperties());
            } else {
                $grouped[$applicable_rule->getSelector()] = $applicable_rule->getProperties();
            }
        }

        return $grouped;
    }

    public function addHtmlToStore($rawHtml)
    {
        $this->htmlStore->addHtmlSnippet($rawHtml);
    }


}