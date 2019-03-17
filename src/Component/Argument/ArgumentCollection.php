<?php
namespace TYPO3Fluid\Fluid\Component\Argument;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class ArgumentCollection implements ArgumentCollectionInterface
{
    /**
     * @var Argument[]
     */
    protected $arguments = [];

    /**
     * @var Argument[]
     */
    protected $undeclared = [];

    public function __construct(ArgumentDefinitionInterface ...$arguments)
    {
        foreach ($arguments as $definition) {
            $this->addDefinition($definition);
        }
    }

    public function assignAll(iterable $values): ArgumentCollectionInterface
    {
        foreach ($values as $name => $value) {
            $this->assign($name, $value);
        }
        return $this;
    }

    public function assign(string $name, $value): ArgumentCollectionInterface
    {
        if (isset($this->arguments[$name])) {
            $this->arguments[$name]->setValue($value);
        } else {
            $argument = new Argument(new UndeclaredArgumentDefinition($name));
            $argument->setValue($value);
            $this->undeclared[$name] = $argument;
        }
        return $this;
    }

    /**
     * @return ArgumentInterface[]
     */
    public function readAll(): iterable
    {
        return array_merge($this->arguments, $this->undeclared);
    }

    public function evaluateAndValidate(RenderingContextInterface $context): array
    {
        $variables = [];
        foreach ($this->readAll() as $argument) {
            $name = $argument->evaluateName($context);
            $value = $argument->evaluateValue($context);
            if ($argument->isRequired() && $value === null) {
                throw new \InvalidArgumentException('Required argument ' . $name . ' not passed');
            }
            $variables[$name] = $value;
        }

        return $variables;
    }

    public function addDefinition(ArgumentDefinitionInterface $definition): ArgumentCollectionInterface
    {
        $argumentName = $definition->getName();
        if (!isset($this->arguments[$argumentName])) {
            $this->arguments[$argumentName] = new Argument($definition);
        }
        return $this;
    }

    public function read(string $argumentName): ArgumentInterface
    {
        if (isset($this->arguments[$argumentName])) {
            return $this->arguments[$argumentName];
        } elseif (isset($this->undeclared[$argumentName])) {
            return $this->undeclared[$argumentName];
        }
        throw new \InvalidArgumentException('Argument ' . $argumentName . ' does not exist in collection');
    }
}
