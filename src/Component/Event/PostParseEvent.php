<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component\Event;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Post-parsing event. Dispatched after the compoent, all
 * arguments and all child components/nodes have been parsed.
 */
class PostParseEvent extends AbstractEvent
{
    const PAYLOAD_PARSING_STATE = 'parsingState';
    const PAYLOAD_NODE = 'node';

    public function __construct(RenderingContextInterface $renderingContext, ?\ArrayAccess $payload = null)
    {
        if (!$payload->offsetExists(static::PAYLOAD_PARSING_STATE)) {
            throw new \InvalidArgumentException(
                static::class . ' must be constructed with payload part: ' . static::PAYLOAD_PARSING_STATE,
                1552821078
            );
        }
        if (!$payload->offsetExists(static::PAYLOAD_NODE)) {
            throw new \InvalidArgumentException(
                static::class . ' must be constructed with payload part: ' . static::PAYLOAD_ARGUMENTS,
                1552821081
            );
        }
        parent::__construct($renderingContext, $payload);
    }

    public function getParsingState(): ParsingState
    {
        return $this->payload->offsetGet(static::PAYLOAD_PARSING_STATE);
    }

    public function getArguments(): ArgumentCollectionInterface
    {
        return $this->payload->offsetGet(static::PAYLOAD_NODE)->createArguments();
    }

    public function getNode(): NodeInterface
    {
        return $this->payload->offsetGet(static::PAYLOAD_NODE);
    }
}