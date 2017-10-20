<?php

namespace CSSFromHTMLExtractor\Css\Rule;

use CSSFromHTMLExtractor\Css\Property\Property;
use Symfony\Component\CssSelector\Node\Specificity;

final class Rule
{
    /**
     * @var string
     */
    private $selector;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var Specificity
     */
    private $specificity;

    /**
     * @var integer
     */
    private $order;

    /** @var string  */
    private $media;

    /**
     * Rule constructor.
     *
     * @param string $media
     * @param string $selector
     * @param Property[] $properties
     * @param Specificity $specificity
     * @param int $order
     */
    public function __construct($media, $selector, array $properties, Specificity $specificity, $order)
    {
        $this->media = $media;
        $this->selector = $selector;
        $this->properties = $properties;
        $this->specificity = $specificity;
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }


    /**
     * Get selector
     *
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get specificity
     *
     * @return Specificity
     */
    public function getSpecificity()
    {
        return $this->specificity;
    }

    /**
     * Get order
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }
}
