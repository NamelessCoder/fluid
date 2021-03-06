<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\ViewHelpers;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Parser\Source;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Inline Fluid rendering ViewHelper
 *
 * Renders Fluid code stored in a variable, which you normally would
 * have to render before assigning it to the view. Instead you can
 * do the following (note, extremely simplified use case):
 *
 *      $view->assign('variable', 'value of my variable');
 *      $view->assign('code', 'My variable: {variable}');
 *
 * And in the template:
 *
 *      {code -> f:inline()}
 *
 * Which outputs:
 *
 *      My variable: value of my variable
 *
 * You can use this to pass smaller and dynamic pieces of Fluid code
 * to templates, as an alternative to creating new partial templates.
 */
class InlineViewHelper extends AbstractViewHelper
{
    protected $escapeChildren = false;

    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'code',
            'string',
            'Fluid code to be rendered as if it were part of the template rendering it. Can be passed as inline argument or tag content'
        );
    }

    public function evaluate(RenderingContextInterface $renderingContext)
    {
        $arguments = $this->getArguments()->setRenderingContext($renderingContext)->getArrayCopy();
        $parsed = $renderingContext->getTemplateParser()->parse(new Source((string) ($arguments['code'] ?? $this->evaluateChildren($renderingContext))));
        $parsed->getArguments()->assignAll($renderingContext->getVariableProvider()->getAll());
        return $parsed->evaluate($renderingContext);
    }
}
