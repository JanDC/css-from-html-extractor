<?php

namespace PageSpecificCss\Twig;

use Twig_Compiler;
use Twig_Node;

class FoldNode extends Twig_Node
{

    /**
     * FoldNode constructor.
     * @param mixed|Twig_Node $body
     * @param int $lineno
     * @param string $tag
     */
    public function __construct(\Twig_NodeInterface $body, $lineno, $tag)
    {
        parent::__construct(['body' => $body], [], $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        // DO STUFF...
    }

}