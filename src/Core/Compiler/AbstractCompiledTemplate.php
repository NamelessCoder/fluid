<?php
namespace TYPO3Fluid\Fluid\Core\Compiler;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\ComponentInterface;
use TYPO3Fluid\Fluid\Component\ContainerComponentInterface;
use TYPO3Fluid\Fluid\Component\NestedComponentInterface;
use TYPO3Fluid\Fluid\Component\Structure\AbstractTemplateComponent;
use TYPO3Fluid\Fluid\Component\Structure\CompiledSection;
use TYPO3Fluid\Fluid\Component\Structure\SectionInterface;
use TYPO3Fluid\Fluid\Core\Parser\ParsedTemplateInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\View\Exception\InvalidSectionException;

/**
 * Abstract Fluid Compiled template.
 *
 * INTERNAL!!
 */
abstract class AbstractCompiledTemplate extends AbstractTemplateComponent implements ParsedTemplateInterface
{

    /**
     * @param string $identifier
     * @return void
     */
    public function setIdentifier($identifier)
    {
        // void, ignored.
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return static::class;
    }

    /**
     * Returns a variable container used in the PostParse Facet.
     *
     * @return VariableProviderInterface
     */
    public function getVariableContainer()
    {
        return new StandardVariableProvider();
    }

    /**
     * Render the parsed template with rendering context
     *
     * @param RenderingContextInterface $renderingContext The rendering context to use
     * @return string Rendered string
     */
    public function render(RenderingContextInterface $renderingContext)
    {
        return '';
    }

    public function evaluate(RenderingContextInterface $context)
    {
        return $this->render($context);
    }

    /**
     * @return boolean
     */
    public function isCompilable()
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isCompiled()
    {
        return true;
    }

    /**
     * @param string $name
     * @return SectionInterface
     */
    public function getSection(string $name): SectionInterface
    {
        $methodNameOfSection = 'section_' . sha1($name);
        if (!method_exists($this, $methodNameOfSection)) {
            if ($this->extends instanceof ParsedTemplateInterface) {
                return $this->extends->getSection($name);
            }
            throw new InvalidSectionException('Section "' . $sectionName . '" does not exist.');
        }
        $self = $this;
        $closure = function(RenderingContextInterface $renderingContext) use ($self, $methodNameOfSection) {
            return $self->$methodNameOfSection($renderingContext);
        };
        return new CompiledSection($name, $closure);
    }

    /**
     * @return boolean
     */
    public function hasLayout()
    {
        return false;
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public function getLayoutName(RenderingContextInterface $renderingContext)
    {
        return '';
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @return void
     */
    public function addCompiledNamespaces(RenderingContextInterface $renderingContext)
    {
    }
}
