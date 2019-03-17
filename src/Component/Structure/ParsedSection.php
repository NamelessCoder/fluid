<?php
namespace TYPO3Fluid\Fluid\Component\Structure;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollection;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;
use TYPO3Fluid\Fluid\Component\Argument\ArgumentDefinitionInterface;
use TYPO3Fluid\Fluid\Component\ContainerComponentInterface;
use TYPO3Fluid\Fluid\Component\NestedComponentInterface;
use TYPO3Fluid\Fluid\Component\OptionInterface;
use TYPO3Fluid\Fluid\Component\ParameterizedComponentInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class ParsedSection extends AbstractSection implements NestedComponentInterface
{
    /**
     * @var NodeInterface
     */
    protected $contentNode;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var NodeInterface
     */
    protected $parent;

    /**
     * @var ?ArgumentCollectionInterface
     */
    protected $parameters;

    /**
     * Parsed section
     *
     * A representation of a Fluid "Section" component which can be rendered with
     * f:render, as it exists before being compiled. Consists of a node which can
     * return content, a node which contains a name and an optional list of parameters
     * which will be used when rendering the section, to validate argument presence,
     * type, assign default values etc.
     *
     * Once compiled (as a function on a compiled PHP class) a section will be
     * represented as a CompiledSection which has the same interface but a vastly
     * different constructor method.
     *
     * @param string $nameNode Name of the section
     * @param NodeInterface $contentNode The node which when evaluated will output the content of the section
     * @param ArgumentCollectionInterface|null $parameters Optional ArgumentCollection from f:parameter in parsed section body
     */
    public function __construct(string $name, NodeInterface $contentNode, ?ArgumentCollectionInterface $parameters = null)
    {
        $this->name = $name;
        $this->contentNode = $contentNode;
        $this->parameters = $parameters ?? new ArgumentCollection();
    }

    public function addParameter(ArgumentDefinitionInterface $definition): \TYPO3Fluid\Fluid\Component\ParameterizedComponentInterface
    {
        $this->parameters->addDefinition($definition);
    }

    public function createArguments(): ArgumentCollectionInterface
    {
        return $this->parameters;
    }

    public function getNode(): NodeInterface
    {
        return $this->contentNode;
    }

    public function evaluate(RenderingContextInterface $context, ?ArgumentCollectionInterface $arguments = null)
    {
        return $this->contentNode->evaluateChildNodes($context);
    }

    public function evaluateName(RenderingContextInterface $context, ?ArgumentCollectionInterface $arguments = null)
    {
        return $this->name;
    }

    public function readOption(string $name): OptionInterface
    {
        
    }

    public function getParent(): ContainerComponentInterface
    {
        return $this->parent;
    }

    public function setParent(ContainerComponentInterface $parent)
    {
        $this->parent = $parent;
    }
}