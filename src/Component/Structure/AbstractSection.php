<?php
namespace TYPO3Fluid\Fluid\Component\Structure;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

abstract class AbstractSection implements SectionInterface
{
    public function evaluateWithArguments(RenderingContextInterface $context, ArgumentCollectionInterface $arguments)
    {
        $variableContainer = $context->getVariableProvider();
        foreach ($arguments->evaluateAndValidate($context) as $name => $value) {
            $variableContainer->add($name, $value);
        }
        return $this->evaluate($context);
    }
}