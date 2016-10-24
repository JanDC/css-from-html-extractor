<?php

namespace PageSpecificCss\Twig\TokenParsers;

use Twig_Error_Syntax;
use Twig_NodeInterface;
use Twig_Token;
use Twig_TokenParser;

class FoldTokenParser extends Twig_TokenParser
{

    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     *
     * @throws Twig_Error_Syntax
     */
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideFoldEnd'], true);
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        return new FoldNode($body, [], $lineno, $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'fold';
    }

    public function decideFoldEnd(Twig_Token $token)
    {
        return $token->test('endfold');
    }
}