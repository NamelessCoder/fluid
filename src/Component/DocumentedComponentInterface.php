<?php
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Named Fluid component interface
 *
 * Implemented by any class that is capable of returning documentation
 * about itself as a string (which can then be collected to document
 * templates, or rendered as output in development contexts when a
 * component throws an error).
 */
interface DocumentedComponentInterface
{
    /**
     * Return a string which documents the use of this component.
     *
     * @return string
     */
    public function getDocumentation(): string;
}
