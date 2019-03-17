<?php
namespace TYPO3Fluid\Fluid\Component\Structure;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollection;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentDefinitionInterface;
use TYPO3Fluid\Fluid\Component\ComponentInterface;
use TYPO3Fluid\Fluid\Component\ContainerComponentInterface;
use TYPO3Fluid\Fluid\Component\ExtendableComponentInterface;
use TYPO3Fluid\Fluid\Component\NamedComponentInterface;
use TYPO3Fluid\Fluid\Component\NestedComponentInterface;
use TYPO3Fluid\Fluid\Component\OptionInterface;
use TYPO3Fluid\Fluid\Component\ParameterizedComponentInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

abstract class AbstractTemplateComponent implements
    ComponentInterface,
    ContainerComponentInterface,
    ExtendableComponentInterface, 
    ParameterizedComponentInterface
{
    /**
     * @var ArgumentCollectionInterface
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $children = [];

    /**
     * @var array
     */
    protected $namedChildren = [];

    /**
     * @var ComponentInterface
     */
    protected $extends;

    public function __construct()
    {
        $this->parameters = new ArgumentCollection();
    }

    public function extend(ComponentInterface $parent)
    {
        $this->extends = $parent;
    }

    public function addChild(NestedComponentInterface $child): ContainerComponentInterface
    {
        $this->children[] = $child;
        return $this;
    }

    public function addNamedChild(string $name, NestedComponentInterface $child): ContainerComponentInterface
    {
        $this->namedChildren[$name] = $child;
        return $this;
    }

    public function getChildren(): iterable
    {
        return $this->children;
    }

    public function getNamedChild(string $name): NestedComponentInterface
    {
        $child = $this->namedChildren[$name] ?? ($this->extends instanceof ContainerComponentInterface ? $this->extends->getNamedChild($name) : null);
        if (!$child) {
            throw new ChildNotFoundException('Named child ' . $name . ' not found', 1552836203);
        }
        return $child;
    }

    public function getParent(): ComponentInterface
    {
        return $this->extends;
    }

    public function addParameter(ArgumentDefinitionInterface $definition): ParameterizedComponentInterface
    {
        $this->parameters->addDefinition($definition);
    }

    public function createArguments(): ArgumentCollectionInterface
    {
        return $this->parameters;
    }

    public function evaluateWithArguments(RenderingContextInterface $context, ArgumentCollectionInterface $arguments)
    {
        $variableContainer = $context->getVariableProvider();
        foreach ($arguments->evaluateAndValidate($context) as $name => $value) {
            $variableContainer->add($name, $value);
        }
        return $this->evaluate($context);
    }
}