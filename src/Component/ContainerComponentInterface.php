<?php
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Fluid container-component interface
 *
 * Implemented by any class that is capable of being rendered
 * in Fluid with a set of nested components. Must be implemented
 * in addition to either vanilla or parameterized ComponentInterface.
 */
interface ContainerComponentInterface
{
    public function addChild(NestedComponentInterface $child): self;

    public function addNamedChild(string $name, NestedComponentInterface $child): self;

    public function getChildren(): iterable;

    public function getNamedChild(string $name): NestedComponentInterface;
}
