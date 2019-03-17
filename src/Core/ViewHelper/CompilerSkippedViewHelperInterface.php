<?php
namespace TYPO3Fluid\Fluid\Core\ViewHelper;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Compiler Skip signal interface
 *
 * Implemented by ViewHelpers that want to be completely ignored
 * by the template compiler, including arguments. Combine with the
 * ParserRuntimeOnly trait for a parsetime-specific ViewHelper.
 */
interface CompilerSkippedViewHelperInterface
{
}
