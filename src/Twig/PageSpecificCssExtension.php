<?php


namespace PageSpecificCss\Twig;

use Twig_Extension;
use Twig_Extension_InitRuntimeInterface;

class PageSpecificCssExtension extends Twig_Extension implements Twig_Extension_InitRuntimeInterface{

public function getTokenParsers()
{
    return [
        new css_TokenParser()
    ];
}

}