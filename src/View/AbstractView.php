<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\View;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * An abstract View
 *
 * @deprecated Will be removed in Fluid 4.0
 */
abstract class AbstractView implements ViewInterface
{

    /**
     * View variables and their values
     * @var array
     * @see assign()
     */
    protected $variables = [];

    /**
     * Renders the view
     *
     * @return mixed The rendered view
     */
    public function render()
    {
        return '';
    }

    /**
     * Add a variable to $this->variables.
     * Can be chained, so $this->view->assign(..., ...)->assign(..., ...); is possible
     *
     * @param string $key Key of variable
     * @param mixed $value Value of object
     * @return self
     */
    public function assign($key, $value): ViewInterface
    {
        $this->variables[$key] = $value;
        return $this;
    }

    /**
     * Add multiple variables to $this->variables.
     *
     * @param array $values array in the format array(key1 => value1, key2 => value2)
     * @return AbstractView an instance of $this, to enable chaining
     */
    public function assignMultiple(array $values): ViewInterface
    {
        foreach ($values as $key => $value) {
            $this->assign($key, $value);
        }
        return $this;
    }
}
