<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Core\Parser\SyntaxTree;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\AbstractComponent;
use TYPO3Fluid\Fluid\Component\ComponentInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Array Syntax Tree Node. Handles JSON-like arrays.
 */
class ArrayNode extends AbstractComponent implements \ArrayAccess
{

    /**
     * An associative array. Each key is a string. Each value is either a literal, or an AbstractNode.
     *
     * @var array
     */
    protected $internalArray = [];

    /**
     * Constructor.
     *
     * @param array $internalArray Array to store
     */
    public function __construct(array $internalArray = [])
    {
        $this->internalArray = $internalArray;
    }

    public function flatten(bool $extractNode = false)
    {
        return $this;
    }

    public function evaluate(RenderingContextInterface $renderingContext): array
    {
        $arrayToBuild = [];
        foreach ($this->internalArray as $key => $value) {
            $arrayToBuild[$key] = $value instanceof ComponentInterface ? $value->evaluate($renderingContext) : $value;
        }
        return $arrayToBuild;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->internalArray[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->internalArray[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->internalArray[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->internalArray[$offset]);
    }

}
