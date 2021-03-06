<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Tests\Unit\ViewHelpers;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\TextNode;
use TYPO3Fluid\Fluid\Tests\Unit\Core\Rendering\RenderingContextFixture;

/**
 * Testcase for SpacelessViewHelper
 */
class SpacelessViewHelperTest extends ViewHelperBaseTestCase
{
    public function getStandardTestValues(): array
    {
        $context = new RenderingContextFixture();
        return [
            'removes spaces' => ['foo', $context, null, [new TextNode('       foo        ')]],
        ];
    }
}
