<?php
namespace TYPO3Fluid\Fluid\Core\Compiler;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Core\Parser\ParsedTemplateInterface;
use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ArrayNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\RootNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class TemplateCompiler
 */
class TemplateCompiler
{

    const SHOULD_GENERATE_VIEWHELPER_INVOCATION = '##should_gen_viewhelper##';
    const MODE_NORMAL = 'normal';
    const MODE_WARMUP = 'warmup';

    /**
     * @var array
     */
    protected $syntaxTreeInstanceCache = [];

    /**
     * @var NodeConverter
     */
    protected $nodeConverter;

    /**
     * @var RenderingContextInterface
     */
    protected $renderingContext;

    /**
     * @var string
     */
    protected $mode = self::MODE_NORMAL;

    /**
     * @var ParsedTemplateInterface
     */
    protected $currentlyProcessingState;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nodeConverter = new NodeConverter($this);
    }

    /**
     * Instruct the TemplateCompiler to enter warmup mode, assigning
     * additional context allowing cache-related implementations to
     * subsequently check the mode.
     *
     * Cannot be reversed once done - should only be used from within
     * FluidCacheWarmerInterface implementations!
     */
    public function enterWarmupMode()
    {
        $this->mode = static::MODE_WARMUP;
    }

    /**
     * Returns TRUE only if the TemplateCompiler is in warmup mode.
     */
    public function isWarmupMode()
    {
        return $this->mode === static::MODE_WARMUP;
    }

    /**
     * @return ParsedTemplateInterface|NULL
     */
    public function getCurrentlyProcessingState()
    {
        return $this->currentlyProcessingState;
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @return void
     */
    public function setRenderingContext(RenderingContextInterface $renderingContext)
    {
        $this->renderingContext = $renderingContext;
    }

    /**
     * @return RenderingContextInterface
     */
    public function getRenderingContext()
    {
        return $this->renderingContext;
    }

    /**
     * @param NodeConverter $nodeConverter
     * @return void
     */
    public function setNodeConverter(NodeConverter $nodeConverter)
    {
        $this->nodeConverter = $nodeConverter;
    }

    /**
     * @return NodeConverter
     */
    public function getNodeConverter()
    {
        return $this->nodeConverter;
    }

    /**
     * @return void
     */
    public function disable()
    {
        throw new StopCompilingException('Compiling stopped');
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return !$this->renderingContext->isCacheEnabled();
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function has($identifier)
    {
        $identifier = $this->sanitizeIdentifier($identifier);

        if (isset($this->syntaxTreeInstanceCache[$identifier]) || class_exists($identifier, false)) {
            return true;
        }
        if (!$this->renderingContext->isCacheEnabled()) {
            return false;
        }
        if (!empty($identifier)) {
            return (boolean) $this->renderingContext->getCache()->get($identifier);
        }
        return false;
    }

    /**
     * @param string $identifier
     * @return ParsedTemplateInterface
     */
    public function get($identifier)
    {
        $identifier = $this->sanitizeIdentifier($identifier);

        if (!isset($this->syntaxTreeInstanceCache[$identifier])) {
            if (!class_exists($identifier, false)) {
                $this->renderingContext->getCache()->get($identifier);
            }
            if (!is_a($identifier, UncompilableTemplateInterface::class, true)) {
                $this->syntaxTreeInstanceCache[$identifier] = new $identifier();
            } else {
                return new $identifier();
            }
        }


        return $this->syntaxTreeInstanceCache[$identifier];
    }

    /**
     * Resets the currently processing state
     *
     * @return void
     */
    public function reset()
    {
        $this->currentlyProcessingState = null;
    }

    /**
     * @param string $identifier
     * @param ParsingState $parsingState
     * @return string|null
     */
    public function store($identifier, ParsingState $parsingState)
    {
        if ($this->isDisabled()) {
            $parsingState->setCompilable(false);
            return null;
        }

        // Macro-optimisation: by storing the *parsed* template in runtime memory until the request is expired,
        // performance is increased many hundred percent when caches are flushed. This makes the TemplateCompiler
        // effectively have a second-level cache internally which prevents loading and execution of the compiled
        // class (which is merely a duplicated version of the parsing state). Even though compiled templates are
        // more efficient than uncompiled parsing states, once parsed, the parsing state is as efficient to render,
        // so the end result is that this second-level cache avoids creating two versions of the same template in
        // the same execution scope.
        $this->syntaxTreeInstanceCache[$identifier] = $parsingState;

        $identifier = $this->sanitizeIdentifier($identifier);
        $cache = $this->renderingContext->getCache();
        if (!$parsingState->isCompilable()) {
            $templateCode = '<?php' . PHP_EOL . 'class ' . $identifier .
                ' extends \TYPO3Fluid\Fluid\Core\Compiler\AbstractCompiledTemplate' . PHP_EOL .
                ' implements \TYPO3Fluid\Fluid\Core\Compiler\UncompilableTemplateInterface' . PHP_EOL .
                '{' . PHP_EOL . '}';
            $cache->set($identifier, $templateCode);
            return $templateCode;
        }

        $this->currentlyProcessingState = $parsingState;
        $this->nodeConverter->setVariableCounter(0);
        $generatedRenderFunctions = $this->generateSectionCodeFromParsingState($parsingState);

        $generatedRenderFunctions .= $this->generateCodeForSection(
            $this->nodeConverter->convertListOfSubNodes($parsingState->getRootNode()),
            'render',
            'Main Render function'
        );

        $classDefinition = 'class ' . $identifier . ' extends \TYPO3Fluid\Fluid\Core\Compiler\AbstractCompiledTemplate';

        $templateCode = <<<EOD
<?php

%s {

public function createArguments(): \TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface
{
    return %s;
}

public function getLayoutName(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface \$renderingContext) {
\$self = \$this;
%s;
}
public function hasLayout() {
return %s;
}
public function addCompiledNamespaces(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface \$renderingContext) {
\$renderingContext->getViewHelperResolver()->addNamespaces(%s);
}

%s

}
EOD;
        $storedLayoutName = $parsingState->getLayoutNameNode();
        $templateCode = sprintf(
            $templateCode,
            $classDefinition,
            $this->generateCodeForCreateArgumentsMethod($parsingState->createArguments()),
            $this->generateCodeForLayoutName($storedLayoutName),
            ($parsingState->hasLayout() ? 'TRUE' : 'FALSE'),
            var_export($this->renderingContext->getViewHelperResolver()->getNamespaces(), true),
            $generatedRenderFunctions
        );
        $this->renderingContext->getCache()->set($identifier, $templateCode);
        return $templateCode;
    }

    protected function generateCodeForCreateArgumentsMethod(ArgumentCollectionInterface $arguments)
    {
        $definitions = [];
        foreach ($arguments->readAll() as $argument) {
            $argumentDefinition = $argument->getDefinition();
            $definitions[] = sprintf(
                'new \TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition(%s, %s, %s, %s, %s)',
                var_export($argumentDefinition->getName(), true),
                var_export($argumentDefinition->getType(), true),
                var_export($argumentDefinition->getDescription(), true),
                var_export($argumentDefinition->isRequired(), true),
                var_export($argumentDefinition->getDefaultValue(), true)
            );
        }
        return 'new \TYPO3Fluid\Fluid\Component\Argument\ArgumentCollection(' . implode(', ', $definitions) . ')';
    }

    /**
     * @param RootNode|string $storedLayoutNameArgument
     * @return string
     */
    protected function generateCodeForLayoutName($storedLayoutNameArgument)
    {
        if ($storedLayoutNameArgument instanceof RootNode) {
            list ($initialization, $execution) = array_values($this->nodeConverter->convertListOfSubNodes($storedLayoutNameArgument));
            return $initialization . PHP_EOL . 'return ' . $execution;
        } else {
            return 'return (string) \'' . $storedLayoutNameArgument . '\'';
        }
    }

    /**
     * @param ParsingState $parsingState
     * @return string
     */
    protected function generateSectionCodeFromParsingState(ParsingState $parsingState)
    {
        $generatedRenderFunctions = '';
        foreach ($parsingState->getSections() as $sectionName => $section) {
            $generatedRenderFunctions .= $this->generateCodeForSection(
                $this->nodeConverter->convertListOfSubNodes($section->getNode()),
                'section_' . sha1($sectionName),
                'section ' . $sectionName
            );
        }
        return $generatedRenderFunctions;
    }

    /**
     * Replaces special characters by underscores
     * @see http://www.php.net/manual/en/language.variables.basics.php
     *
     * @param string $identifier
     * @return string the sanitized identifier
     */
    protected function sanitizeIdentifier($identifier)
    {
        return preg_replace('([^a-zA-Z0-9_\x7f-\xff])', '_', $identifier);
    }

    /**
     * @param array $converted
     * @param string $expectedFunctionName
     * @param string $comment
     * @return string
     */
    protected function generateCodeForSection(array $converted, $expectedFunctionName, $comment)
    {
        $templateCode = <<<EOD
/**
 * %s
 */
public function %s(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface \$renderingContext) {
\$self = \$this;
%s
return %s;
}

EOD;
        return sprintf($templateCode, $comment, $expectedFunctionName, $converted['initialization'], $converted['execution']);
    }

    /**
     * Returns a unique variable name by appending a global index to the given prefix
     *
     * @param string $prefix
     * @return string
     */
    public function variableName($prefix)
    {
        return $this->nodeConverter->variableName($prefix);
    }

    /**
     * @param NodeInterface $node
     * @return string
     */
    public function wrapChildNodesInClosure(NodeInterface $node)
    {
        $closure = '';
        $closure .= 'function() use ($renderingContext, $self) {' . chr(10);
        $convertedSubNodes = $this->nodeConverter->convertListOfSubNodes($node);
        $closure .= $convertedSubNodes['initialization'];
        $closure .= sprintf('return %s;', $convertedSubNodes['execution']) . chr(10);
        $closure .= '}' . PHP_EOL;
        return $closure;
    }

    /**
     * Wraps one ViewHelper argument evaluation in a closure that can be
     * rendered by passing a rendering context.
     *
     * @param ViewHelperNode $node
     * @param string $argumentName
     * @return string
     */
    public function wrapViewHelperNodeArgumentEvaluationInClosure(ViewHelperNode $node, $argumentName)
    {
        $arguments = $node->getArguments();
        $argument = $arguments[$argumentName];
        $closure = 'function() use ($renderingContext, $self) {' . chr(10);
        $compiled = $this->nodeConverter->convert($argument);
        if (!empty($compiled['initialization'])) {
            $closure .= $compiled['initialization'] . chr(10);
        }
        $closure .= 'return ' . $compiled['execution'] . ';' . chr(10);
        $closure .= '}';
        return $closure;
    }
}
