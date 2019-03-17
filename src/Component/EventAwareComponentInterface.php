<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Component\Event\EventInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Event-aware Fluid component interface
 *
 * Implemented components that wish to receive events. A component implementing
 * this interface can return a different component (of the same type) that will
 * then be used as substitute after the event.
 */
interface EventAwareComponentInterface
{
    /**
     * Evaluate the component name by passing the rendering context.
     * Does not receive variables; for this purpose, a specific
     * interface ParameterizedComponentInterface must be implemented.
     *
     * @param RenderingContextInterface $context
     * @return mixed
     */
    public function handleEvent(EventInterface $event): self;
}
