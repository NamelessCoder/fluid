<?php
namespace TYPO3Fluid\Fluid\View;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentValidator;
use TYPO3Fluid\Fluid\Component\Structure\ChildNotFoundException;
use TYPO3Fluid\Fluid\Core\Cache\FluidCacheInterface;
use TYPO3Fluid\Fluid\Core\Parser\ParsedTemplateInterface;
use TYPO3Fluid\Fluid\Core\Parser\PassthroughSourceException;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3Fluid\Fluid\View\Exception\InvalidSectionException;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;
use TYPO3Fluid\Fluid\ViewHelpers\SectionViewHelper;

/**
 * Abstract Fluid Template View.
 *
 * Contains the fundamental methods which any Fluid based template view needs.
 */
abstract class AbstractTemplateView extends AbstractView
{

    /**
     * Constants defining possible rendering types
     */
    const RENDERING_TEMPLATE = 1;
    const RENDERING_PARTIAL = 2;
    const RENDERING_LAYOUT = 3;

    /**
     * The initial rendering context for this template view.
     * Due to the rendering stack, another rendering context might be active
     * at certain points while rendering the template.
     *
     * @var RenderingContextInterface
     */
    protected $baseRenderingContext;

    /**
     * Stack containing the current rendering type, the current rendering context, and the current parsed template
     * Do not manipulate directly, instead use the methods"getCurrent*()", "startRendering(...)" and "stopRendering()"
     *
     * @var array
     */
    protected $renderingStack = [];

    /**
     * Constructor
     *
     * @param null|RenderingContextInterface $context
     */
    public function __construct(RenderingContextInterface $context = null)
    {
        if (!$context) {
            $context = new RenderingContext($this);
            $context->setControllerName('Default');
            $context->setControllerAction('Default');
        }
        $this->setRenderingContext($context);
    }

    /**
     * Initialize the RenderingContext. This method can be overridden in your
     * View implementation to manipulate the rendering context *before* it is
     * passed during rendering.
     */
    public function initializeRenderingContext()
    {
        $this->baseRenderingContext->getViewHelperVariableContainer()->setView($this);
    }

    /**
     * Sets the cache to use in RenderingContext.
     *
     * @param FluidCacheInterface $cache
     * @return void
     */
    public function setCache(FluidCacheInterface $cache)
    {
        $this->baseRenderingContext->setCache($cache);
    }

    /**
     * Gets the TemplatePaths instance from RenderingContext
     *
     * @return TemplatePaths
     */
    public function getTemplatePaths()
    {
        return $this->baseRenderingContext->getTemplatePaths();
    }

    /**
     * Gets the ViewHelperResolver instance from RenderingContext
     *
     * @return ViewHelperResolver
     */
    public function getViewHelperResolver()
    {
        return $this->baseRenderingContext->getViewHelperResolver();
    }

    /**
     * Gets the RenderingContext used by the View
     *
     * @return RenderingContextInterface
     */
    public function getRenderingContext()
    {
        return $this->baseRenderingContext;
    }

    /**
     * Injects a fresh rendering context
     *
     * @param RenderingContextInterface $renderingContext
     * @return void
     */
    public function setRenderingContext(RenderingContextInterface $renderingContext)
    {
        $this->baseRenderingContext = $renderingContext;
        $this->initializeRenderingContext();
    }

    /**
     * Assign a value to the variable container.
     *
     * @param string $key The key of a view variable to set
     * @param mixed $value The value of the view variable
     * @return $this
     * @api
     */
    public function assign($key, $value)
    {
        $this->baseRenderingContext->getVariableProvider()->add($key, $value);
        return $this;
    }

    /**
     * Assigns multiple values to the JSON output.
     * However, only the key "value" is accepted.
     *
     * @param array $values Keys and values - only a value with key "value" is considered
     * @return $this
     * @api
     */
    public function assignMultiple(array $values)
    {
        $templateVariableContainer = $this->baseRenderingContext->getVariableProvider();
        foreach ($values as $key => $value) {
            $templateVariableContainer->add($key, $value);
        }
        return $this;
    }

    /**
     * Loads the template source and render the template.
     * If "layoutName" is set in a PostParseFacet callback, it will render the file with the given layout.
     *
     * @param string|null $actionName If set, this action's template will be rendered instead of the one defined in the context.
     * @return string Rendered Template
     * @api
     */
    public function render($actionName = null)
    {
        $renderingContext = $this->getCurrentRenderingContext();
        $templateParser = $renderingContext->getTemplateParser();
        $templatePaths = $renderingContext->getTemplatePaths();
        if ($actionName) {
            $actionName = ucfirst($actionName);
            $renderingContext->setControllerAction($actionName);
        }
        try {
            $parsedTemplate = $this->getCurrentParsedTemplate();
        } catch (PassthroughSourceException $error) {
            return $error->getSource();
        }

        $variables = $renderingContext->getVariableProvider()->getAll();
        $arguments = $parsedTemplate->createArguments()->assignAll($variables);

        if (!$parsedTemplate->hasLayout()) {
            $this->startRendering($parsedTemplate, $renderingContext);
            $output = $parsedTemplate->evaluateWithArguments($renderingContext, $arguments);
            $this->stopRendering();
        } else {
            $layoutName = $parsedTemplate->getLayoutName($renderingContext);
            try {
                $parsedLayout = $templateParser->getOrParseAndStoreTemplate(
                    $templatePaths->getLayoutIdentifier($layoutName),
                    function($parent, TemplatePaths $paths) use ($layoutName) {
                        return $paths->getLayoutSource($layoutName);
                    }
                );
                $parsedLayout->extend($parsedTemplate);
            } catch (PassthroughSourceException $error) {
                return $error->getSource();
            }
        }

        $this->startRendering($parsedTemplate, $renderingContext);
        $output = ($parsedLayout ?? $parsedTemplate)->evaluateWithArguments($renderingContext, $arguments);
        $this->stopRendering();

        return $output;
    }

    /**
     * Renders a given section.
     *
     * @param string $sectionName Name of section to render
     * @param array $variables The variables to use
     * @param boolean $ignoreUnknown Ignore an unknown section and just return an empty string
     * @return string rendered template for the section
     * @throws InvalidSectionException
     */
    public function renderSection($sectionName, array $variables = [], $ignoreUnknown = false)
    {
        $renderingContext = $this->getCurrentRenderingContext();
        $output = '';

        $variables = array_merge($renderingContext->getVariableProvider()->getAll(), $variables);

        $renderingContext = clone $renderingContext;
        $renderingContext->setVariableProvider($renderingContext->getVariableProvider()->getScopeCopy($variables));

        try {
            $parsedTemplate = $this->getCurrentParsedTemplate();

            $this->startRendering($parsedTemplate, $renderingContext);

            $section = $parsedTemplate->getSection($sectionName);
            $parameters = $section->createArguments();
            $parameters->assignAll($variables);

            $output = $section->evaluateWithArguments($renderingContext, $parameters);

            $this->stopRendering();

        } catch (PassthroughSourceException $error) {
            return $error->getSource();
        } catch (InvalidTemplateResourceException $error) {
            if (!$ignoreUnknown) {
                return $renderingContext->getErrorHandler()->handleViewError($error);
            }
        } catch (InvalidSectionException $error) {
            if (!$ignoreUnknown) {
                $output = $renderingContext->getErrorHandler()->handleViewError($error);
            }
        } catch (ChildNotFoundException $error) {
            if (!$ignoreUnknown) {
                $output = $renderingContext->getErrorHandler()->handleViewError($error);
            }
        } catch (Exception $error) {
            $output = $renderingContext->getErrorHandler()->handleViewError($error);
        }

        return $output;
    }

    /**
     * Renders a partial.
     *
     * @param string $partialName
     * @param string $sectionName
     * @param array $variables
     * @param boolean $ignoreUnknown Ignore an unknown section and just return an empty string
     * @return string
     */
    public function renderPartial($partialName, $sectionName, array $variables, $ignoreUnknown = false)
    {
        $templatePaths = $this->baseRenderingContext->getTemplatePaths();
        $renderingContext = clone $this->getCurrentRenderingContext();
        try {
            $parsedPartial = $renderingContext->getTemplateParser()->getOrParseAndStoreTemplate(
                $templatePaths->getPartialIdentifier($partialName),
                function ($parent, TemplatePaths $paths) use ($partialName) {
                    return $paths->getPartialSource($partialName);
                }
            );

        } catch (PassthroughSourceException $error) {
            return $error->getSource();
        } catch (InvalidTemplateResourceException $error) {
            if (!$ignoreUnknown) {
                return $renderingContext->getErrorHandler()->handleViewError($error);
            }
            return '';
        } catch (InvalidSectionException $error) {
            if (!$ignoreUnknown) {
                return $renderingContext->getErrorHandler()->handleViewError($error);
            }
            return '';
        } catch (Exception $error) {
            return $renderingContext->getErrorHandler()->handleViewError($error);
        }
        $variables = $parsedPartial->createArguments()->assignAll($variables)->evaluateAndValidate($renderingContext);
        $renderingContext->setVariableProvider($renderingContext->getVariableProvider()->getScopeCopy($variables));
        $this->startRendering($parsedPartial, $renderingContext);
        if ($sectionName !== null) {
            $output = $this->renderSection($sectionName, $variables, $ignoreUnknown);
        } else {
            $output = $parsedPartial->render($renderingContext);
        }
        $this->stopRendering();
        return $output;
    }

    /**
     * Start a new nested rendering. Pushes the given information onto the $renderingStack.
     *
     * @param ParsedTemplateInterface $template
     * @param RenderingContextInterface $context
     * @return void
     */
    protected function startRendering(ParsedTemplateInterface $template, RenderingContextInterface $context)
    {
        array_push($this->renderingStack, ['parsedTemplate' => $template, 'renderingContext' => $context]);
    }

    /**
     * Stops the current rendering. Removes one element from the $renderingStack. Make sure to always call this
     * method pair-wise with startRendering().
     *
     * @return void
     */
    protected function stopRendering()
    {
        $this->getCurrentRenderingContext()->getTemplateCompiler()->reset();
        array_pop($this->renderingStack);
    }

    /**
     * Get the parsed template which is currently being rendered or compiled.
     *
     * @return ParsedTemplateInterface
     */
    protected function getCurrentParsedTemplate()
    {
        $currentRendering = end($this->renderingStack);
        $renderingContext = $this->getCurrentRenderingContext();
        $parsedTemplate = $currentRendering['parsedTemplate'] ? $currentRendering['parsedTemplate'] : $renderingContext->getTemplateCompiler()->getCurrentlyProcessingState();
        if ($parsedTemplate) {
            return $parsedTemplate;
        }
        $templatePaths = $renderingContext->getTemplatePaths();
        $templateParser = $renderingContext->getTemplateParser();
        $controllerName = $renderingContext->getControllerName();
        $actionName = $renderingContext->getControllerAction();
        $parsedTemplate = $templateParser->getOrParseAndStoreTemplate(
            $templatePaths->getTemplateIdentifier($controllerName, $actionName),
            function($parent, TemplatePaths $paths) use ($controllerName, $actionName, $renderingContext) {
                return $paths->getTemplateSource($controllerName, $actionName);
            }
        );
        if ($parsedTemplate->isCompiled()) {
            $parsedTemplate->addCompiledNamespaces($this->baseRenderingContext);
        }
        return $parsedTemplate;
    }

    /**
     * Get the rendering context which is currently used.
     *
     * @return RenderingContextInterface
     */
    protected function getCurrentRenderingContext()
    {
        $currentRendering = end($this->renderingStack);
        return $currentRendering['renderingContext'] ? $currentRendering['renderingContext'] : $this->baseRenderingContext;
    }
}
