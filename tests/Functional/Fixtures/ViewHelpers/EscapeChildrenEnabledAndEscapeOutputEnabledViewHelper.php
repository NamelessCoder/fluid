<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Tests\Functional\Fixtures\ViewHelpers;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Class EscapeChildrenEnabledAndEscapeOutputEnabledViewHelper
 */
class EscapeChildrenEnabledAndEscapeOutputEnabledViewHelper extends AbstractEscapingViewHelper
{

    protected $escapeChildren = true;
    protected $escapeOutput = true;
}
