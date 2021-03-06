<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\ViewHelpers;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Space Removal ViewHelper
 *
 * Removes redundant spaces between HTML tags while
 * preserving the whitespace that may be inside HTML
 * tags. Trims the final result before output.
 *
 * Heavily inspired by Twig's corresponding node type.
 *
 * <code title="Usage of f:spaceless">
 * <f:spaceless>
 * <div>
 *     <div>
 *         <div>text
 *
 * text</div>
 *     </div>
 * </div>
 * </code>
 * <output>
 * <div><div><div>text
 *
 * text</div></div></div>
 * </output>
 */
class SpacelessViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function evaluate(RenderingContextInterface $renderingContext)
    {
        return trim(preg_replace('/\\>\\s+\\</', '><', $this->evaluateChildren($renderingContext)));
    }
}
