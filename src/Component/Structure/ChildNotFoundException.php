<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component\Structure;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Exception;

/**
 * Exception thrown when getChild() / getNamedChild() on ContainerComponentInterface
 * cannot find a child by the given name/index.
 */
class ChildNotFoundException extends Exception
{
}