<?php

namespace CSSFromHTMLExtractor;

use CSSFromHTMLExtractor\Css\Property\Property;
use CSSFromHTMLExtractor\Css\Rule\Rule;

class CssStore
{
    /** @var array Property objects, grouped by selector */
    private $styles = [];

    /** @var string|null */
    private $charset;

    public function addCssStyles($cssRules)
    {
        $this->styles = array_merge($this->styles, $cssRules);

        return $this;
    }

    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @return $this
     */
    public function purge()
    {
        $this->styles = [];

        return $this;
    }

    /**
     * @param string $path
     *
     * @return bool whether the dumping was successful
     */
    public function dumpStyles($path)
    {
        return file_put_contents($path, $this->compileStyles()) === false;
    }

    public function compileStyles()
    {
        // Structure rules in order, by media query
        $styles = $this->prepareStylesForProcessing();

        $prefix = is_null($this->charset) ? '' : $this->charset;

        return $prefix . join(
                '',
                array_map(
                    function ($styleGroup) {
                        $media = key($styleGroup);
                        $rules = reset($styleGroup);

                        return $this->parseMediaToString($media, $rules);
                    },
                    $styles
                )
            );
    }

    /**
     * @param string $media
     * @param array $rules
     *
     * @return string
     *
     */
    private function parseMediaToString($media, array $rules)
    {
        if ($media == '') {
            return
                join(
                    '',
                    array_map(
                        function (Rule $rule) {
                            return $this->parsePropertiesToString($rule->getSelector(), $rule->getProperties());
                        },
                        $rules
                    )
                );

        }

        return "$media { " . join(
                '',
                array_map(
                    function (Rule $rule) {
                        return $this->parsePropertiesToString($rule->getSelector(), $rule->getProperties());
                    },
                    $rules
                )
            ) . "}";
    }

    /**
     *
     * @param string $selector
     * @param array $properties
     *
     * @return string
     */
    private function parsePropertiesToString($selector, array $properties)
    {
        return "$selector { " .
            join(
                '',
                array_map(
                    function (Property $property) {
                        return $property->getName() . ': ' . $property->getValue() . ';';
                    },
                    $properties
                )
            ) .
            "}";
    }

    private function prepareStylesForProcessing()
    {
        // Group styles by order and media
        $groupedStyles = [];

        /** @var Rule $style */
        foreach ($this->styles as $style) {
            $groupedStyles[$style->getOrder()][$style->getMedia()][] = $style;
        }

        return $groupedStyles;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
}
