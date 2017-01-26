<?php

namespace PageSpecificCss;

use PageSpecificCss\Css\Property\Property;

class CssStore
{
    /** @var array Property objects, grouped by selector */
    private $styles = [];

    public function addCssStyles($cssRules)
    {
        $this->styles = array_merge_recursive($this->styles, $cssRules);
        return $this;
    }

    public function getStyles()
    {
        return $this->styles;
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
        return join('', array_map(function ($properties, $key) {
            return $this->parseMediaToString($key, $properties);
        }, $this->styles, array_keys($this->styles)));
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
                join('', array_map(function ($properties, $selector) {
                        return $this->parsePropertiesToString($selector, $properties);
                    }, $rules, array_keys($rules))
                );

        }

        return "$media { " . join('', array_map(function ($properties, $selector) {
                    return $this->parsePropertiesToString($selector, $properties);
                }, $rules, array_keys($rules))
            ) . "}";


    }

    /**
     *
     * @return string
     *
     */
    private function parsePropertiesToString($selector, array $properties)
    {
        return "$selector { " .
            join('', array_map(function (Property $property) {
                    return $property->getName() . ': ' . $property->getValue() . ';';
                }, $properties)
            ) .
            "}";
    }
}