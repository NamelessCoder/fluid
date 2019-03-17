<?php
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Type Specific Fluid component interface
 *
 * Implemented by any class that requires or has a certain type
 * associated with it, e.g. an argument or an option.
 *
 * Validation is encapsulated in the component as to allow third
 * party components to require, validate and transform values.
 *
 * For example: a third party framework might want an argument
 * component to support a custom type like `ClassA|ClassB` or
 * various annotations of iterators with certain types within.
 */
interface TypeSpecificComponentInterface extends ValuedComponentInterface
{
    /**
     * Get the type of this component. Can be a string php type name, a
     * specific class, etc.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Evaluate (validate) the type of the variable contained
     * as value, as determined by methods on ValuedComponentInterface.
     *
     * @param RenderingContextInterface $context
     * @param ArgumentCollectionInterface|null $arguments
     * @return mixed
     */
    public function evaluateType(RenderingContextInterface $context, ?ArgumentCollectionInterface $arguments = null);
}
