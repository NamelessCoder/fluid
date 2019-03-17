<?php
declare(strict_types=1);

namespace TYPO3Fluid\Fluid\ViewHelpers;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Component\Event\EventInterface;
use TYPO3Fluid\Fluid\Component\Event\PostParseEvent;
use TYPO3Fluid\Fluid\Component\EventAwareComponentInterface;
use TYPO3Fluid\Fluid\Component\ParameterizedComponentInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\ParserRuntimeOnly;

class OptionViewHelper extends AbstractViewHelper implements EventAwareComponentInterface
{
    use ParserRuntimeOnly;

    public function initializeArguments()
    {
        $this->registerArgument('value', 'string', 'Type of parameter (string, int, bool, class name, ...)', true);
    }

    public function handleEvent(EventInterface $event) : EventAwareComponentInterface
    {
        if ($event instanceof PostParseEvent) {
            $event->getNode()
        }
        return $this;
    }
}