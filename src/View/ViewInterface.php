<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\View;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Interface of a view
 *
 * @deprecated Will be removed in Fluid 4.0
 */
interface ViewInterface
{
    /**
     * Add a variable to the view data collection.
     * Can be chained, so $this->view->assign(..., ...)->assign(..., ...); is possible
     *
     * @param string $key Key of variable
     * @param mixed $value Value of object
     * @return ViewInterface an instance of $this, to enable chaining
     */
    public function assign($key, $value): self;

    /**
     * Add multiple variables to the view data collection
     *
     * @param array $values array in the format array(key1 => value1, key2 => value2)
     * @return ViewInterface an instance of $this, to enable chaining
     */
    public function assignMultiple(array $values): self;

    /**
     * Renders the view
     *
     * @return mixed The rendered view
     */
    public function render();

    /**
     * Renders a given section.
     *
     * @param string $sectionName Name of section to render
     * @param array $variables The variables to use
     * @param boolean $ignoreUnknown Ignore an unknown section and just return an empty string
     * @return mixed rendered template for the section
     */
    public function renderSection(string $sectionName, array $variables = [], bool $ignoreUnknown = false);

    /**
     * Renders a partial.
     *
     * @param string $partialName
     * @param string|null $sectionName
     * @param array $variables
     * @param boolean $ignoreUnknown Ignore an unknown section and just return an empty string
     * @return mixed
     */
    public function renderPartial(string $partialName, ?string $sectionName, array $variables, bool $ignoreUnknown = false);
}
