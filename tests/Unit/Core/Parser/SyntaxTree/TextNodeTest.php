<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Tests\Unit\Core\Parser\SyntaxTree;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\TextNode;
use TYPO3Fluid\Fluid\Tests\Unit\Core\Rendering\RenderingContextFixture;
use TYPO3Fluid\Fluid\Tests\UnitTestCase;

/**
 * Testcase for TextNode
 */
class TextNodeTest extends UnitTestCase
{
    /**
     * @test
     */
    public function flattenReturnsSelf(): void
    {
        $subject = new TextNode('');
        $this->assertSame($subject, $subject->flatten());
    }

    /**
     * @test
     */
    public function flattenReturnsTextWithExtractTrue(): void
    {
        $subject = new TextNode('foo');
        $this->assertSame('foo', $subject->flatten(true));
    }

    /**
     * @test
     */
    public function renderReturnsSameStringAsGivenInConstructor(): void
    {
        $string = 'I can work quite effectively in a train!';
        $node = new TextNode($string);
        $renderingContext = new RenderingContextFixture();
        $this->assertEquals($node->evaluate($renderingContext), $string, 'The rendered string of a text node is not the same as the string given in the constructor.');
    }
}
