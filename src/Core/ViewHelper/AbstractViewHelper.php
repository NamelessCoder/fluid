<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Core\ViewHelper;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use Closure;
use TYPO3Fluid\Fluid\Component\AbstractComponent;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollection;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentDefinition;
use TYPO3Fluid\Fluid\Component\ComponentInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * The abstract base class for all view helpers.
 */
abstract class AbstractViewHelper extends AbstractComponent
{
    /**
     * @var RenderingContextInterface
     */
    protected $renderingContext;

    protected $escapeOutput = true;

    /**
     * Execute via Component API implementation.
     *
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public function evaluate(RenderingContextInterface $renderingContext)
    {
        $this->renderingContext = $renderingContext;
        $this->getArguments()->setRenderingContext($renderingContext);
        return $this->callRenderMethod();
    }

    public function onOpen(RenderingContextInterface $renderingContext): ComponentInterface
    {
        $this->getArguments()->setRenderingContext($renderingContext);
        return parent::onOpen($renderingContext);
    }

    public function getArguments(): ArgumentCollection
    {
        if ($this->arguments === null) {
            $this->arguments = new ArgumentCollection();
            $this->initializeArguments();
        }
        return $this->arguments;
    }

    /**
     * Register a new argument. Call this method from your ViewHelper subclass
     * inside the initializeArguments() method.
     *
     * @param string $name Name of the argument
     * @param string $type Type of the argument
     * @param string $description Description of the argument
     * @param boolean $required If TRUE, argument is required. Defaults to FALSE.
     * @param mixed $defaultValue Default value of argument
     * @return AbstractViewHelper $this, to allow chaining.
     * @throws Exception
     */
    protected function registerArgument(string $name, string $type, string $description, bool $required = false, $defaultValue = null): self
    {
        $this->getArguments()->addDefinition(new ArgumentDefinition($name, $type, $description, $required, $defaultValue));
        return $this;
    }

    /**
     * Overrides a registered argument. Call this method from your ViewHelper subclass
     * inside the initializeArguments() method if you want to override a previously registered argument.
     *
     * @param string $name Name of the argument
     * @param string $type Type of the argument
     * @param string $description Description of the argument
     * @param boolean $required If TRUE, argument is required. Defaults to FALSE.
     * @param mixed $defaultValue Default value of argument
     * @return AbstractViewHelper $this, to allow chaining.
     * @throws Exception
     * @see registerArgument()
     * @deprecated Will be removed in Fluid 4.0
     */
    protected function overrideArgument(string $name, string $type, string $description, bool $required = false, $defaultValue = null): self
    {
        $this->getArguments()->addDefinition(new ArgumentDefinition($name, $type, $description, $required, $defaultValue));
        return $this;
    }

    /**
     * Call the render() method and handle errors.
     *
     * @return mixed the rendered ViewHelper
     * @throws Exception
     * @deprecated Will be removed and no longer called in Fluid 4.0
     */
    protected function callRenderMethod()
    {
        if (method_exists($this, 'render')) {
            return call_user_func([$this, 'render']);
        }
        if (method_exists($this, 'renderStatic')) {
            // Method is safe to call - will not recurse through ViewHelperInvoker via the default
            // implementation of renderStatic() on this class.
            return call_user_func_array([static::class, 'renderStatic'], [$this->arguments->getArrayCopy(), $this->buildRenderChildrenClosure(), $this->arguments->getRenderingContext()]);
        }
        return $this->renderChildren();
    }

    /**
     * Helper method which triggers the rendering of everything between the
     * opening and the closing tag.
     *
     * @return mixed The finally rendered child nodes.
     */
    protected function renderChildren()
    {
        return $this->evaluateChildren($this->renderingContext);
    }

    /**
     * Helper which is mostly needed when calling renderStatic() from within
     * render().
     *
     * No public API yet.
     *
     * @deprecated Will be removed and not called in Fluid 4.0
     * @return Closure
     */
    protected function buildRenderChildrenClosure()
    {
        $self = clone $this;
        $renderChildrenClosure = function () use ($self) {
            return $self->renderChildren();
        };
        return $renderChildrenClosure;
    }

    /**
     * Initialize all arguments. You need to override this method and call
     * $this->registerArgument(...) inside this method, to register all your arguments.
     *
     * @return void
     */
    protected function initializeArguments()
    {
    }

    public function allowUndeclaredArgument(string $argumentName): bool
    {
        return false;
    }

    protected function hasArgument(string $argumentName): bool
    {
        return $this->getArguments()->getRaw($argumentName) !== null;
    }
}
