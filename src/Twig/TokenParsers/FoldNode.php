<?php

namespace PageSpecificCss\Twig\TokenParsers;

use Twig_Compiler;
use Twig_Node;

class FoldNode extends Twig_Node
{

    public function __construct(Twig_Node $body, array $attributes, $lineno, $tag)
    {
        parent::__construct(['body' => $body], $attributes, $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        file_put_contents('test', $this->getNode('body'));
    }
}