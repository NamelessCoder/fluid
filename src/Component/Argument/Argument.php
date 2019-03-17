<?php
namespace TYPO3Fluid\Fluid\Component\Argument;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\ValuedComponentInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class Argument implements ArgumentInterface
{
    /**
     * @var ArgumentDefinitionInterface
     */
    protected $definition;

    protected $value;

    public function __construct(ArgumentDefinitionInterface $definition)
    {
        $this->definition = $definition;
    }

    public function evaluate(RenderingContextInterface $context)
    {
        return $this->value ?? $this->definition->getDefaultValue();
    }

    public function getDefinition(): ArgumentDefinitionInterface
    {
        return $this->definition;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function evaluateName(RenderingContextInterface $context, ?ArgumentCollectionInterface $arguments = null)
    {
        return $this->definition->getName();
    }

    public function getDefaultValue()
    {
        return $this->definition->getDefaultValue();
    }

    public function isRequired()
    {
        return $this->definition->isRequired();
    }

    public function evaluateValue(RenderingContextInterface $context, ?ArgumentCollectionInterface $arguments = null)
    {
        return $this->value instanceof NodeInterface ? $this->value->evaluate($context) : $this->value;
    }
}
