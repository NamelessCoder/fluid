<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Extendable Fluid component interface
 *
 * Implemented by any class that is capable of being rendered
 * in Fluid and has a name, e.g. a section.
 */
interface ExtendableComponentInterface
{
    /**
     * Tell this component to extend a specific parent.
     *
     * @param ComponentInterface $parent
     * @return mixed
     */
    public function extend(ComponentInterface $parent);
    
    public function getParent(): ComponentInterface;
}
