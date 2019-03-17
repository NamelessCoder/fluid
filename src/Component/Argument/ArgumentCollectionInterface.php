<?php
namespace TYPO3Fluid\Fluid\Component\Argument;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

interface ArgumentCollectionInterface
{
    public function __construct(ArgumentDefinitionInterface ...$arguments);

    public function assignAll(iterable $values): self;

    public function assign(string $argumentName, $value): self;

    public function evaluateAndValidate(RenderingContextInterface $context): array;

    /**
     * @return ArgumentInterface[]
     */
    public function readAll(): iterable;

    public function read(string $argumentName): ArgumentInterface;

    public function addDefinition(ArgumentDefinitionInterface $definition): ArgumentCollectionInterface;
}
