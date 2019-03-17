<?php
namespace TYPO3Fluid\Fluid\ViewHelpers;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Event\EventInterface;
use TYPO3Fluid\Fluid\Component\Event\PostParseEvent;
use TYPO3Fluid\Fluid\Component\EventAwareComponentInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\TextNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\CompilerSkippedViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\PostParseInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\TemplateVariableContainer;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\ParserRuntimeOnly;

/**
 * With this tag, you can select a layout to be used for the current template.
 *
 * = Examples =
 *
 * <code>
 * <f:layout name="main" />
 * </code>
 * <output>
 * (no output)
 * </output>
 *
 * @api
 */
class LayoutViewHelper extends AbstractViewHelper implements EventAwareComponentInterface, CompilerSkippedViewHelperInterface
{
    use ParserRuntimeOnly;

    /**
     * Initialize arguments
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('name', 'string', 'Name of layout to use. If none given, "Default" is used.');
    }

    public function handleEvent(EventInterface $event): EventAwareComponentInterface
    {
        if ($event instanceof PostParseEvent) {
            $event->getParsingState()->setLayoutNameNode($event->getNode()->getArguments()['name'] ?? new TextNode('Default'));
        }
        return $this;
    }
}
