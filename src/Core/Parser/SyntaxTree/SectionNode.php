<?php
namespace TYPO3Fluid\Fluid\Core\Parser\SyntaxTree;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollection;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentDefinitionInterface;
use TYPO3Fluid\Fluid\Component\ParameterizedComponentInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Node used for sections (substitutes ViewHelperNode when
 * ViewHelper is a section ViewHelper).
 */
class SectionNode extends AbstractNode implements ParameterizedComponentInterface
{
    protected $name = '';

    /**
     * @var ArgumentCollectionInterface
     */
    protected $parameters;
    
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->parameters = new ArgumentCollection();
    }

    /**
     * Evaluate the section node, by evaluating the subtree.
     *
     * @param RenderingContextInterface $renderingContext
     * @return mixed Evaluated subtree
     */
    public function evaluate(RenderingContextInterface $renderingContext)
    {
        return $this->evaluateChildNodes($renderingContext);
    }

    public function evaluateWithArguments(RenderingContextInterface $renderingContext, ArgumentCollectionInterface $arguments)
    {
        return $this->evaluateChildNodes($renderingContext);
    }

    public function addParameter(ArgumentDefinitionInterface $definition): ParameterizedComponentInterface
    {
        $this->parameters->addDefinition($definition);
        return $this;
    }
}
