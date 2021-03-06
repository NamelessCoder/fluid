<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Tests\Unit\View;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Tests\UnitTestCase;
use TYPO3Fluid\Fluid\View\AbstractView;

/**
 * Testcase for the AbstractView
 */
class AbstractViewTest extends UnitTestCase
{

    /**
     * @test
     */
    public function testParentRenderMethodReturnsEmptyString(): void
    {
        $instance = $this->getMockForAbstractClass(AbstractView::class);
        $result = $instance->render();
        $this->assertEquals('', $result);
    }

    /**
     * @test
     */
    public function testAssignsVariableAndReturnsSelf(): void
    {
        $mock = $this->getMockForAbstractClass(AbstractView::class);
        $mock->assign('test', 'foobar');
        $this->assertAttributeEquals(['test' => 'foobar'], 'variables', $mock);
    }

    /**
     * @test
     */
    public function testAssignsMultipleVariablesAndReturnsSelf(): void
    {
        $mock = $this->getMockForAbstractClass(AbstractView::class);
        $mock->assignMultiple(['test' => 'foobar', 'baz' => 'barfoo']);
        $this->assertAttributeEquals(['test' => 'foobar', 'baz' => 'barfoo'], 'variables', $mock);
    }
}
