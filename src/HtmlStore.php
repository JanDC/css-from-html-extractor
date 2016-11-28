<?php

namespace PageSpecificCss;

class HtmlStore
{
    /** @var array Property objects, grouped by selector */
    private $snippets = [];

    public function addHtmlSnippet($htmlSnippet)
    {
        $this->snippets = array_merge($this->snippets, [$htmlSnippet]);
        return $this;
    }

    public function getSnippets()
    {
        return $this->snippets;
    }
}