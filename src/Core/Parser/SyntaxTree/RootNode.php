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
 * Root node of every syntax tree.
 */
class RootNode extends AbstractNode implements ParameterizedComponentInterface
{
    /**
     * @var ArgumentCollectionInterface
     */
    protected $parameters;

    public function __construct()
    {
        $this->parameters = new ArgumentCollection();
    }

    /**
     * Evaluate the root node, by evaluating the subtree.
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

    public function createArguments(): ArgumentCollectionInterface
    {
        return $this->parameters;
    }

    public function addParameter(ArgumentDefinitionInterface $definition): ParameterizedComponentInterface
    {
        $this->parameters->addDefinition($definition);
        return $this;
    }
}
