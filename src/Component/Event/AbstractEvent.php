<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Component\Event;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

abstract class AbstractEvent implements EventInterface
{
    /** @var \ArrayAccess */
    protected $payload;

    protected $renderingContext;

    public function __construct(RenderingContextInterface $renderingContext, ?\ArrayAccess $payload = null)
    {
        $this->renderingContext = $renderingContext;
        $this->payload = $payload ?? new \ArrayObject();
    }

    public function getPayload(): \ArrayAccess
    {
        return $this->payload;
    }

    public function getRenderingContext(): RenderingContextInterface
    {
        return $this->renderingContext;
    }
}