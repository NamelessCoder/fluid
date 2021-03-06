<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollection;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Basic Fluid component interface
 *
 * Implemented by any class that is capable of being rendered
 * in Fluid with or without arguments.
 */
interface ComponentInterface
{
    public function onOpen(RenderingContextInterface $renderingContext): self;

    public function onClose(RenderingContextInterface $renderingContext): self;

    /**
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public function evaluate(RenderingContextInterface $renderingContext);

    /**
     * Returns one of the following:
     *
     * - Itself, if there is more than one child node and one or more nodes are not TextNode or NumericNode
     * - A plain value if there is a single child node of type TextNode or NumericNode
     * - The one child node if there is only a single child node not of type TextNode or NumericNode
     * - Null if there are no child nodes at all.
     *
     * @param bool $extractNode If TRUE, will extract the value of a single node if the node type contains a scalar value
     * @return ComponentInterface|string|int|float|null
     */
    public function flatten(bool $extractNode = false);

    /**
     * Creates a (or returns a stored) collection of arguments based on parameter
     * definitions of this component, ready to be filled with arguments that will
     * be passed to execute()
     *
     * @return ArgumentCollection
     */
    public function getArguments(): ArgumentCollection;

    public function setArguments(ArgumentCollection $arguments): self;

    public function allowUndeclaredArgument(string $argumentName): bool;

    public function getName(): ?string;

    public function addChild(ComponentInterface $component): self;

    public function getNamedChild(string $name): ComponentInterface;

    public function getTypedChildren(string $typeClassName, ?string $name = null): ComponentInterface;

    /**
     * @return iterable|ComponentInterface[]
     */
    public function getChildren(): iterable;

    /**
     * @param iterable|ComponentInterface[] $children
     * @return ComponentInterface
     */
    public function setChildren(iterable $children): self;

    /**
     * Returns whether the escaping interceptors should be disabled or enabled for the render-result of children of this ViewHelper
     *
     * Note: This method is no public API, use $this->escapeChildren instead!
     *
     * @return boolean
     */
    public function isChildrenEscapingEnabled(): bool;

    /**
     * Returns whether the escaping interceptors should be disabled or enabled for the render-result of this ViewHelper
     *
     * Note: This method is no public API, use $this->escapeOutput instead!
     *
     * @return boolean
     */
    public function isOutputEscapingEnabled(): bool;
}
