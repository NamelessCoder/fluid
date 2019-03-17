<?php
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Fluid nested component interface
 *
 * Implemented by any class that is capable of being assigned
 * to a parent ContainerComponentInterface implementation.
 *
 * Must be implemented in addition to vanilla or parameterized
 * ComponentInterface, and can be combined with other types of
 * component interfaces, e.g. child can itself be a container.
 */
interface NestedComponentInterface
{
    public function getParent(): ContainerComponentInterface;
    public function setParent(ContainerComponentInterface $parent);
}
