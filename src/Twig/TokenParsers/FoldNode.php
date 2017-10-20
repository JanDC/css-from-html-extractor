<?php

namespace CSSFromHTMLExtractor\Twig\TokenParsers;

use CSSFromHTMLExtractor\Twig\Extension;
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
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("echo \$this->env->getExtension('".Extension::class."')->addCssToExtract(")
            ->raw('trim(ob_get_clean())')
            ->raw(");\n");
    }
}