<?php
namespace TYPO3Fluid\Fluid\Core\Parser\SyntaxTree;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Parser;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Extension node (contains name of extended template)
 */
class ExtendsNode extends AbstractNode
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name Name of the extended template
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Return the name of the extended template.
     *
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public function evaluate(RenderingContextInterface $renderingContext)
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return $this->getText();
    }
}
