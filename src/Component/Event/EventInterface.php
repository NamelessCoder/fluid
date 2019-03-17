<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component\Event;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Common Event Interface
 *
 * Interface shared by all event types. Specific event types may
 * implement additional public methods (to return different parts
 * from the payload data).
 *
 * The payload data should be an iterable which returns both keys
 * and values, with the keys being the name of the payload part.
 * 
 * The constructor is however enforced to ensure that the event
 * can be consistently created with a reference to the rendering
 * context that applied when the event fired, plus an optional
 * set of payload data.
 */
interface EventInterface
{
    public function __construct(RenderingContextInterface $renderingContext, \ArrayAccess $payload = null);
    public function getPayload(): \ArrayAccess;
    public function getRenderingContext(): RenderingContextInterface;
}