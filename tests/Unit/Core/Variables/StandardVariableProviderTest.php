<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Tests\Unit\Core\Variables;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Tests\Unit\Core\Fixtures\ArrayAccessDummy;
use TYPO3Fluid\Fluid\Tests\Unit\ViewHelpers\Fixtures\UserWithoutToString;
use TYPO3Fluid\Fluid\Tests\UnitTestCase;

/**
 * Testcase for TemplateVariableContainer
 */
class StandardVariableProviderTest extends UnitTestCase
{

    /**
     * @var StandardVariableProvider
     */
    protected $variableProvider;

    public function setUp(): void
    {
        $this->variableProvider = $this->getMock(StandardVariableProvider::class, ['dummy']);
    }

    public function tearDown(): void
    {
        unset($this->variableProvider);
    }

    /**
     * @dataProvider getOperabilityTestValues
     * @param array $input
     * @param array $expected
     */
    public function testOperability(array $input, array $expected): void
    {
        $provider = new StandardVariableProvider();
        $provider->setSource($input);
        $this->assertEquals($input, $provider->getSource());
        $this->assertEquals($expected, $provider->getAll());
        $this->assertEquals(array_keys($expected), $provider->getAllIdentifiers());
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $provider->get($key));
        }
    }

    /**
     * @return array
     */
    public function getOperabilityTestValues(): array
    {
        return [
            [[], []],
            [['foo' => 'bar'], ['foo' => 'bar']]
        ];
    }

    /**
     * @test
     */
    public function testSupportsDottedPath(): void
    {
        $provider = new StandardVariableProvider();
        $provider->setSource(['foo' => ['bar' => 'baz']]);
        $result = $provider->getByPath('foo.bar');
        $this->assertEquals('baz', $result);
    }

    /**
     * @test
     */
    public function addedObjectsCanBeRetrievedAgain(): void
    {
        $object = 'StringObject';
        $this->variableProvider->add('variable', $object);
        $this->assertSame($this->variableProvider->get('variable'), $object, 'The retrieved object from the context is not the same as the stored object.');
    }

    /**
     * @test
     */
    public function addedObjectsExistInArray(): void
    {
        $object = 'StringObject';
        $this->variableProvider->add('variable', $object);
        $this->assertTrue($this->variableProvider->exists('variable'));
    }

    /**
     * @test
     */
    public function addedObjectsExistInAllIdentifiers(): void
    {
        $object = 'StringObject';
        $this->variableProvider->add('variable', $object);
        $this->assertEquals($this->variableProvider->getAllIdentifiers(), ['variable'], 'Added key is not visible in getAllIdentifiers');
    }

    /**
     * @test
     */
    public function gettingNonexistentValueReturnsNull(): void
    {
        $result = $this->variableProvider->get('nonexistent');
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function removeReallyRemovesVariables(): void
    {
        $this->variableProvider->add('variable', 'string1');
        $this->variableProvider->remove('variable');
        $result = $this->variableProvider->get('variable');
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function getAllShouldReturnAllVariables(): void
    {
        $this->variableProvider->add('name', 'Simon');
        $this->assertSame(['name' => 'Simon'], $this->variableProvider->getAll());
    }

    /**
     * @test
     */
    public function testGetScopeCopyReturnsCopyWithSettings(): void
    {
        $subject = new StandardVariableProvider(['foo' => 'bar', 'settings' => ['baz' => 'bam']]);
        $copy = $subject->getScopeCopy(['bar' => 'foo']);
        $this->assertAttributeEquals(['settings' => ['baz' => 'bam'], 'bar' => 'foo'], 'variables', $copy);
    }

    /**
     * @param mixed $subject
     * @param string $path
     * @param mixed $expected
     * @test
     * @dataProvider getPathTestValues
     */
    public function testGetByPath($subject, string $path, $expected): void
    {
        $provider = new StandardVariableProvider($subject);
        $result = $provider->getByPath($path);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getPathTestValues(): array
    {
        $namedUser = new UserWithoutToString('Foobar Name');
        $unnamedUser = new UserWithoutToString('');
        return [
            [['foo' => 'bar'], 'foo', 'bar'],
            [['foo' => 'bar'], 'foo.invalid', null],
            [['user' => $namedUser], 'user.name', 'Foobar Name'],
            [['user' => $unnamedUser], 'user.name', ''],
            [['user' => $namedUser], 'user.named', true],
            [['user' => $unnamedUser], 'user.named', false],
            [['user' => $namedUser], 'user.invalid', null],
            [['user' => $namedUser], 'user.hasAccessor', true],
            [['user' => $namedUser], 'user.isAccessor', true],
            [['user' => $unnamedUser], 'user.hasAccessor', false],
            [['user' => $unnamedUser], 'user.isAccessor', false],
        ];
    }

    /**
     * @param array $subject
     * @param string $path
     * @param array $expected
     * @test
     * @dataProvider getAccessorsForPathTestValues
     */
    public function testGetAccessorsForPath(array $subject, string $path, array $expected): void
    {
        $provider = new StandardVariableProvider($subject);
        $result = $provider->getAccessorsForPath($path);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAccessorsForPathTestValues(): array
    {
        $namedUser = new UserWithoutToString('Foobar Name');
        $inArray = ['user' => $namedUser];
        $inArrayAccess = new ArrayAccessDummy($inArray);
        $inPublic = (object) $inArray;
        $asArray = StandardVariableProvider::ACCESSOR_ARRAY;
        $asGetter = StandardVariableProvider::ACCESSOR_GETTER;
        $asPublic = StandardVariableProvider::ACCESSOR_PUBLICPROPERTY;
        return [
            [['inArray' => $inArray], 'inArray.user', [$asArray, $asArray]],
            [['inArray' => $inArray], 'inArray.user.name', [$asArray, $asArray, $asGetter]],
            [['inArrayAccessWithGetter' => $inArrayAccess], 'inArrayAccessWithGetter.user.name', [$asArray, $asArray, $asGetter]],
            [['inArrayAccess' => $inArrayAccess], 'inArrayAccess.foo', [$asArray, $asArray]],
            [['inPublic' => $inPublic], 'inPublic.user.name', [$asArray, $asPublic, $asGetter]],
            [['inArrayWithNotFoundTailingPath' => $inArray], 'inArray.user.notfound.void', [$asArray]],
        ];
    }

    /**
     * @param mixed $subject
     * @param string $path
     * @param string|null $accessor
     * @param mixed $expected
     * @test
     * @dataProvider getExtractRedetectAccessorTestValues
     */
    public function testExtractRedetectsAccessorIfUnusableAccessorPassed($subject, string $path, ?string $accessor, $expected): void
    {
        $provider = new StandardVariableProvider($subject);
        $result = $provider->getByPath($path, [$accessor]);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getExtractRedetectAccessorTestValues(): array
    {
        return [
            [['test' => 'test'], 'test', null, 'test'],
            [['test' => 'test'], 'test', 'garbageextractionname', 'test'],
            [['test' => 'test'], 'test', StandardVariableProvider::ACCESSOR_PUBLICPROPERTY, 'test'],
            [['test' => 'test'], 'test', StandardVariableProvider::ACCESSOR_GETTER, 'test'],
            [['test' => 'test'], 'test', StandardVariableProvider::ACCESSOR_ASSERTER, 'test'],
            [['test' => ['array' => new ArrayAccessDummy(['sub' => 'sub'])]], 'test.array.sub', StandardVariableProvider::ACCESSOR_ARRAY, 'sub'],
        ];
    }
}
