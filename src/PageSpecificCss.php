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
    private $rules;

    /**
     * PageSpecificCss constructor.
     * @param string $sourceCss path to the source css
     */
    public function __construct($sourceCss)
    {
        parent::__construct();

        $this->cssStore = new CssStore();
        $this->processor = new Processor();
        $this->rules = $this->processor->getRules(file_get_contents($sourceCss));
        $this->cssConverter = new CssSelectorConverter();

    }

    public function getStore(){
        return $this->cssStore;
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
}