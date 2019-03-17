<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component\Event;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Component\Argument\ArgumentCollectionInterface;

/**
 * Pre-parsing event. Dispatched as soon as the component
 * instance is resolved/created before child content and
 * arguments are parsed.
 */
class PreParseEvent extends AbstractEvent
{
    const PAYLOAD_PARSING_STATE = 'parsingState';

    public function __construct(RenderingContextInterface $renderingContext, ?\ArrayAccess $payload = null)
    {
        if (!isset($payload[static::PAYLOAD_PARSING_STATE])) {
            throw new \InvalidArgumentException(
                static::class . ' must be constructed with payload ' . static::PAYLOAD_PARSING_STATE,
                1552821078
            );
        }
    }

    public function getParsingState(): ParsingState
    {
        return $this->payload[static::PAYLOAD_PARSING_STATE];
    }

}