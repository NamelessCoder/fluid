<?php
namespace TYPO3Fluid\Fluid\Component\Structure;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollection;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentDefinitionInterface;
use TYPO3Fluid\Fluid\Component\OptionInterface;
use TYPO3Fluid\Fluid\Component\ParameterizedComponentInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Representation of a Fluid section once compiled.
 * Is basically a wrapper around a closure that calls
 * a method on the compiled class.
 */
class CompiledSection extends AbstractSection
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $closure;
    
    public function __construct(string $name, \Closure $closure)
    {
        $this->name = $name;
        $this->closure = $closure;
    }

    public function createArguments(): ArgumentCollectionInterface
    {
        return new ArgumentCollection();
    }

    public function evaluate(RenderingContextInterface $context)
    {
        return call_user_func_array($this->closure, [$context]);
    }

    public function evaluateName(RenderingContextInterface $context, ?ArgumentCollectionInterface $arguments = null)
    {
        return $this->name;
    }

    public function readOption(string $name): OptionInterface
    {
        // TODO: Implement readOption() method.
    }

    public function addParameter(ArgumentDefinitionInterface $definition): \TYPO3Fluid\Fluid\Component\ParameterizedComponentInterface
    {
        // TODO: Implement addParameter() method.
    }
}