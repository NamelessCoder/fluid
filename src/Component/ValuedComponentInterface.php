<?php
namespace TYPO3Fluid\Fluid\Component;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Valued Fluid component interface
 *
 * Implemented by any class that is capable of having a value,
 * e.g. an argument or an option.
 */
interface ValuedComponentInterface
{
    /**
     * Set a value. If your component requires a specific value type,
     * this can be type-hinted (making the component more strict when
     * being rendered, possibly causing (catchable) php errors).
     *
     * @param mixed $value
     * @return self
     */
    public function setValue($value);

    /**
     * Get the default value that will apply if no value is provided.
     * You can also return such a value from evaluateValue() but a
     * static value should at least be returned from this method.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Returns TRUE if the component MUST receive a value, false if it
     * does not require one (and possibly has a default value).
     *$
     * @return boolean TRUE if argument is optional
     */
    public function isRequired();

    /**
     * Evaluate the value of the component, with any arguments
     * if the component also supports arguments.
     *
     * @param RenderingContextInterface $context
     * @param ArgumentCollectionInterface|null $arguments
     * @return mixed
     */
    public function evaluateValue(RenderingContextInterface $context, ?ArgumentCollectionInterface $arguments = null);
}
