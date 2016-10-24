<?php

namespace PageSpecificCss;

class CssStore
{
    /** @var string[] */
    private $styles = [];

    public function addCssStyle($cssRules)
    {
        $this->styles[] = $cssRules;
        return $this;
    }

    public function getStyles()
    {
        return $this->styles;
    }

    public function compileStyles()
    {
        return join(';', $this->styles);
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


}