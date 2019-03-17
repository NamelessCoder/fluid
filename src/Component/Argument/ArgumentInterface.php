<?php
namespace TYPO3Fluid\Fluid\Component\Argument;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\ComponentInterface;
use TYPO3Fluid\Fluid\Component\NamedComponentInterface;
use TYPO3Fluid\Fluid\Component\ValuedComponentInterface;

interface ArgumentInterface extends ComponentInterface, NamedComponentInterface, ValuedComponentInterface
{
    public function __construct(ArgumentDefinitionInterface $definition);

    public function getDefinition(): ArgumentDefinitionInterface;
}
