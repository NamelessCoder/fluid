<?php
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentDefinitionInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Parameterized Fluid component interface
 *
 * Implemented by any class that is capable of being rendered
 * in Fluid with arguments.
 *
 * When arguments are provided, evaluateWithArguments() will
 * be called. When they are not, evaluate() will be called.
 */
interface ParameterizedComponentInterface
{
    /**
     * Evaluate the component by passing the rendering context
     * and any arguments passed to the rendering of the component.
     *
     * @param RenderingContextInterface $context
     * @param ArgumentCollectionInterface|null $arguments
     * @return mixed
     */
    public function evaluateWithArguments(RenderingContextInterface $context, ArgumentCollectionInterface $arguments);

    /**
     * Adds a parameter to the parameterized component.
     *
     * @param ArgumentDefinitionInterface $definition
     * @return ParameterizedComponentInterface
     */
    public function addParameter(ArgumentDefinitionInterface $definition): self;

    /**
     * Creates a collection of arguments based on parameter
     * definitions of this component, ready to be filled with
     * arguments that will be passed to evaluateWithArguments()
     *
     * @return ArgumentCollectionInterface
     */
    public function createArguments(): ArgumentCollectionInterface;
}
