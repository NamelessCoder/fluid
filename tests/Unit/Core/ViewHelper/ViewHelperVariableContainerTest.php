<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Tests\Unit\Core\ViewHelper;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3Fluid\Fluid\Tests\Unit\Core\Fixtures\TestViewHelper;
use TYPO3Fluid\Fluid\Tests\UnitTestCase;
use TYPO3Fluid\Fluid\View\AbstractTemplateView;

/**
 * Testcase for AbstractViewHelper
 */
class ViewHelperVariableContainerTest extends UnitTestCase
{

    /**
     * @var ViewHelperVariableContainer
     */
    protected $viewHelperVariableContainer;

    protected function setUp(): void
    {
        $this->viewHelperVariableContainer = new ViewHelperVariableContainer();
    }

    /**
     * @test
     */
    public function storedDataCanBeReadOutAgain(): void
    {
        $variable = 'Hello world';
        $this->assertFalse($this->viewHelperVariableContainer->exists(TestViewHelper::class, 'test'));
        $this->viewHelperVariableContainer->add(TestViewHelper::class, 'test', $variable);
        $this->assertTrue($this->viewHelperVariableContainer->exists(TestViewHelper::class, 'test'));

        $this->assertEquals($variable, $this->viewHelperVariableContainer->get(TestViewHelper::class, 'test'));
    }

    /**
     * @test
     */
    public function addOrUpdateSetsAKeyIfItDoesNotExistYet(): void
    {
        $this->viewHelperVariableContainer->add('Foo\Bar', 'nonExistentKey', 'value1');
        $this->assertEquals($this->viewHelperVariableContainer->get('Foo\Bar', 'nonExistentKey'), 'value1');
    }

    /**
     * @test
     */
    public function addOrUpdateOverridesAnExistingKey(): void
    {
        $this->viewHelperVariableContainer->add('Foo\Bar', 'someKey', 'value1');
        $this->viewHelperVariableContainer->addOrUpdate('Foo\Bar', 'someKey', 'value2');
        $this->assertEquals($this->viewHelperVariableContainer->get('Foo\Bar', 'someKey'), 'value2');
    }

    /**
     * @test
     */
    public function aSetValueCanBeRemovedAgain(): void
    {
        $this->viewHelperVariableContainer->add('Foo\Bar', 'nonExistentKey', 'value1');
        $this->viewHelperVariableContainer->remove('Foo\Bar', 'nonExistentKey');
        $this->assertFalse($this->viewHelperVariableContainer->exists('Foo\Bar', 'nonExistentKey'));
    }

    /**
     * @test
     */
    public function existsReturnsFalseIfTheSpecifiedKeyDoesNotExist(): void
    {
        $this->assertFalse($this->viewHelperVariableContainer->exists('Foo\Bar', 'nonExistentKey'));
    }

    /**
     * @test
     */
    public function existsReturnsTrueIfTheSpecifiedKeyExists(): void
    {
        $this->viewHelperVariableContainer->add('Foo\Bar', 'someKey', 'someValue');
        $this->assertTrue($this->viewHelperVariableContainer->exists('Foo\Bar', 'someKey'));
    }

    /**
     * @test
     */
    public function existsReturnsTrueIfTheSpecifiedKeyExistsAndIsNull(): void
    {
        $this->viewHelperVariableContainer->add('Foo\Bar', 'someKey', null);
        $this->assertTrue($this->viewHelperVariableContainer->exists('Foo\Bar', 'someKey'));
    }

    /**
     * @test
     */
    public function viewCanBeReadOutAgain(): void
    {
        $view = $this->getMockForAbstractClass(AbstractTemplateView::class);
        $this->viewHelperVariableContainer->setView($view);
        $this->assertSame($view, $this->viewHelperVariableContainer->getView());
    }

    /**
     * @test
     */
    public function getAllGetsAllVariables(): void
    {
        $this->viewHelperVariableContainer->addAll('Foo\\Bar', ['foo' => 'foo', 'bar' => 'bar']);
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $this->viewHelperVariableContainer->getAll('Foo\\Bar'));
    }

    /**
     * @test
     */
    public function getAllReturnsDefaultIfNotFound(): void
    {
        $this->viewHelperVariableContainer->addAll('Foo\\Bar', ['foo' => 'foo']);
        $this->assertSame(['foo' => 'bar'], $this->viewHelperVariableContainer->getAll('Baz\\Baz', ['foo' => 'bar']));
    }

    /**
     * @test
     */
    public function testSleepReturnsExpectedPropertyNames(): void
    {
        $subject = new ViewHelperVariableContainer();
        $properties = $subject->__sleep();
        $this->assertContains('objects', $properties);
    }

    /**
     * @test
     */
    public function testGetReturnsDefaultIfRequestedVariableDoesNotExist(): void
    {
        $subject = new ViewHelperVariableContainer();
        $this->assertEquals('test', $subject->get('foo', 'bar', 'test'));
    }
}
