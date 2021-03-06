<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component\Argument;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Argument definition of each view helper argument
 */
class ArgumentDefinition
{
    /**
     * Name of argument
     *
     * @var string
     */
    protected $name;

    /**
     * Type of argument
     *
     * @var string
     */
    protected $type;

    /**
     * Description of argument
     *
     * @var string
     */
    protected $description;

    /**
     * Is argument required?
     *
     * @var boolean
     */
    protected $required = false;

    /**
     * Default value for argument
     *
     * @var mixed
     */
    protected $defaultValue = null;

    /**
     * Constructor for this argument definition.
     *
     * @param string $name Name of argument
     * @param string $type Type of argument
     * @param string $description Description of argument
     * @param boolean $required TRUE if argument is required
     * @param mixed $defaultValue Default value
     */
    public function __construct(string $name, string $type, string $description, bool $required, $defaultValue = null)
    {
        $this->name = $name;
        $this->type = $type === 'bool' ? 'boolean' : $type;
        $this->description = $description;
        $this->required = $required;
        $this->defaultValue = $defaultValue;
    }

    /**
     * Get the name of the argument
     *
     * @return string Name of argument
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the type of the argument
     *
     * @return string Type of argument
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the description of the argument
     *
     * @return string Description of argument
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the optionality of the argument
     *
     * @return boolean TRUE if argument is optional
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Get the default value, if set
     *
     * @return mixed Default value
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
