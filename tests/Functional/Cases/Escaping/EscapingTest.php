<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Tests\Functional\Cases\Escaping;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Tests\Functional\BaseFunctionalTestCase;

/**
 * Class EscapingTest
 */
class EscapingTest extends BaseFunctionalTestCase
{

    /**
     * @var array
     */
    protected $variables = ['settings' => ['test' => '<strong>Bla</strong>']];

    /**
     * @return array
     */
    public function getTemplateCodeFixturesAndExpectations(): array
    {
        return [
            'escapeChildren can be disabled in template' => [
                '{@escaping false}<test:escapeChildrenEnabledAndEscapeOutputDisabled>{settings.test}</test:escapeChildrenEnabledAndEscapeOutputDisabled>',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'escapeOutput can be disabled in template' => [
                '{@escaping false}<test:escapeChildrenDisabledAndEscapeOutputEnabled>{settings.test}</test:escapeChildrenDisabledAndEscapeOutputEnabled>',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Tag syntax with children, properly encodes variable value' => [
                '<test:escapeChildrenEnabledAndEscapeOutputDisabled>{settings.test}</test:escapeChildrenEnabledAndEscapeOutputDisabled>',
                $this->variables,
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
                ['<strong>Bla</strong>'],
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Inline syntax with children, properly encodes variable value' => [
                '{settings.test -> test:escapeChildrenEnabledAndEscapeOutputDisabled()}',
                $this->variables,
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
                ['<strong>Bla</strong>'],
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Tag syntax with argument, does not encode variable value' => [
                '<test:escapeChildrenEnabledAndEscapeOutputDisabled content="{settings.test}" />',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Inline syntax with argument, does not encode variable value' => [
                '{test:escapeChildrenEnabledAndEscapeOutputDisabled(content: settings.test)}',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Inline syntax with string, does not encode string value' => [
                '{test:escapeChildrenEnabledAndEscapeOutputDisabled(content: \'<strong>Bla</strong>\')}',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Inline syntax with argument in quotes, does encode variable value (encoded before passed to VH)' => [
                '{test:escapeChildrenEnabledAndEscapeOutputDisabled(content: \'{settings.test}\')}',
                $this->variables,
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
                ['<strong>Bla</strong>'],
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Tag syntax with nested inline syntax and children rendering, does not encode variable value' => [
                '<test:escapeChildrenEnabledAndEscapeOutputDisabled content="{settings.test -> test:escapeChildrenEnabledAndEscapeOutputDisabled()}" />',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Tag syntax with nested inline syntax and argument in inline, does not encode variable value' => [
                '<test:escapeChildrenEnabledAndEscapeOutputDisabled content="{test:escapeChildrenEnabledAndEscapeOutputDisabled(content: settings.test)}" />',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Tag syntax with children, does not encode variable value' => [
                '<test:escapeChildrenDisabledAndEscapeOutputDisabled>{settings.test}</test:escapeChildrenDisabledAndEscapeOutputDisabled>',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Inline syntax with children, does not encode variable value' => [
                '{settings.test -> test:escapeChildrenDisabledAndEscapeOutputDisabled()}',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Tag syntax with argument, does not encode variable value' => [
                '<test:escapeChildrenDisabledAndEscapeOutputDisabled content="{settings.test}" />',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Inline syntax with argument, does not encode variable value' => [
                '{test:escapeChildrenDisabledAndEscapeOutputDisabled(content: settings.test)}',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Inline syntax with string, does not encode string value' => [
                '{test:escapeChildrenDisabledAndEscapeOutputDisabled(content: \'<strong>Bla</strong>\')}',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Inline syntax with argument in quotes, does not encode variable value (argument escaping disabled)' => [
                '{test:escapeChildrenDisabledAndEscapeOutputDisabled(content: \'{settings.test}\')}',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Tag syntax with nested inline syntax and children rendering, does not encode variable value' => [
                '<test:escapeChildrenDisabledAndEscapeOutputDisabled content="{settings.test -> test:escapeChildrenDisabledAndEscapeOutputDisabled()}" />',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Tag syntax with nested inline syntax and argument in inline, does not encode variable value' => [
                '<test:escapeChildrenDisabledAndEscapeOutputDisabled content="{test:escapeChildrenDisabledAndEscapeOutputDisabled(content: settings.test)}" />',
                $this->variables,
                ['<strong>Bla</strong>'],
                ['&lt;strong&gt;Bla&lt;/strong&gt;'],
            ],
        ];
    }
}
