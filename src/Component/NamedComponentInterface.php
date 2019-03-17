<?php
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Named Fluid component interface
 *
 * Implemented by any class that is capable of being rendered
 * in Fluid and has a name, e.g. a section.
 */
interface NamedComponentInterface
{
    /**
     * Evaluate the component name by passing the rendering context.
     * Does not receive variables; for this purpose, a specific
     * interface ParameterizedComponentInterface must be implemented.
     *
     * @param RenderingContextInterface $context
     * @param ArgumentCollectionInterface|null $arguments
     * @return mixed
     */
    public function evaluateName(RenderingContextInterface $context, ?ArgumentCollectionInterface $arguments = null);
}
