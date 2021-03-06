<?php
declare(strict_types=1);
namespace TYPO3Fluid\Fluid\Core\Variables;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Class StandardVariableProvider
 */
class StandardVariableProvider implements VariableProviderInterface
{
    const ACCESSOR_ARRAY = 'array';
    const ACCESSOR_GETTER = 'getter';
    const ACCESSOR_ASSERTER = 'asserter';
    const ACCESSOR_PUBLICPROPERTY = 'public';

    /**
     * Variables stored in context
     *
     * @var mixed
     */
    protected $variables = [];

    /**
     * Variables, if any, with which to initialize this
     * VariableProvider.
     *
     * @param array $variables
     */
    public function __construct(array $variables = [])
    {
        $this->variables = $variables;
    }

    /**
     * @param array $variables
     * @return VariableProviderInterface
     */
    public function getScopeCopy(array $variables): VariableProviderInterface
    {
        if (!isset($variables['settings']) && isset($this->variables['settings'])) {
            $variables['settings'] = $this->variables['settings'];
        }
        return new static($variables);
    }

    /**
     * Set the source data used by this VariableProvider. The
     * source can be any type, but the type must of course be
     * supported by the VariableProvider itself.
     *
     * @param mixed $source
     * @return void
     */
    public function setSource($source): void
    {
        $this->variables = $source;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->variables;
    }

    /**
     * Get every variable provisioned by the VariableProvider
     * implementing the interface. Must return an array or
     * ArrayAccess instance!
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->variables;
    }

    /**
     * Add a variable to the context
     *
     * @param string $identifier Identifier of the variable to add
     * @param mixed $value The variable's value
     * @return void
     */
    public function add(string $identifier, $value): void
    {
        $this->variables[$identifier] = $value;
    }

    /**
     * Get a variable from the context. Throws exception if variable is not found in context.
     *
     * If "_all" is given as identifier, all variables are returned in an array,
     * if one of the other reserved variables are given, their appropriate value
     * they're representing is returned.
     *
     * @param string $identifier
     * @return mixed The variable value identified by $identifier
     */
    public function get(string $identifier)
    {
        return $this->getByPath($identifier);
    }

    /**
     * Get a variable by dotted path expression, retrieving the
     * variable from nested arrays/objects one segment at a time.
     * If the second variable is passed, it is expected to contain
     * extraction method names (constants from VariableExtractor)
     * which indicate how each value is extracted.
     *
     * @param string $path
     * @param array $accessors Optional list of accessors (see class constants)
     * @return mixed
     */
    public function getByPath(string $path, array $accessors = [])
    {
        $subject = $this->variables;
        foreach (explode('.', $path) as $index => $pathSegment) {
            $accessor = isset($accessors[$index]) ? $accessors[$index] : null;
            $subject = $this->extractSingleValue($subject, $pathSegment, $accessor);
            if ($subject === null) {
                break;
            }
        }
        return $subject;
    }

    /**
     * Remove a variable from context. Throws exception if variable is not found in context.
     *
     * @param string $identifier The identifier to remove
     * @return void
     */
    public function remove(string $identifier): void
    {
        unset($this->variables[$identifier]);
    }

    /**
     * Returns an array of all identifiers available in the context.
     *
     * @return array Array of identifier strings
     */
    public function getAllIdentifiers(): array
    {
        return array_keys($this->variables);
    }

    /**
     * Checks if this property exists in the VariableContainer.
     *
     * @param string $identifier
     * @return boolean TRUE if $identifier exists, FALSE otherwise
     */
    public function exists(string $identifier): bool
    {
        return isset($this->variables[$identifier]);
    }

    /**
     * @param string $propertyPath
     * @return array
     */
    public function getAccessorsForPath(string $propertyPath): array
    {
        $subject = $this->variables;
        $accessors = [];
        $propertyPathSegments = explode('.', $propertyPath);
        foreach ($propertyPathSegments as $pathSegment) {
            $accessor = $this->detectAccessor($subject, $pathSegment);
            if ($accessor === null) {
                // Note: this may include cases of sub-variable references. When such
                // a reference is encountered the accessor chain is stopped and new
                // accessors will be detected for the sub-variable and all following
                // path segments since the variable is now fully dynamic.
                break;
            }
            $accessors[] = $accessor;
            $subject = $this->extractSingleValue($subject, $pathSegment);
        }
        return $accessors;
    }

    /**
     * Extracts a single value from an array or object.
     *
     * @param mixed $subject
     * @param string $propertyName
     * @param string|null $accessor
     * @return mixed
     */
    protected function extractSingleValue($subject, string $propertyName, ?string $accessor = null)
    {
        if (!$accessor || !$this->canExtractWithAccessor($subject, $propertyName, $accessor)) {
            $accessor = $this->detectAccessor($subject, $propertyName);
        }
        return $this->extractWithAccessor($subject, $propertyName, $accessor);
    }

    /**
     * Returns TRUE if the data type of $subject is potentially compatible
     * with the $accessor.
     *
     * @param mixed $subject
     * @param string $propertyName
     * @param string $accessor
     * @return boolean
     */
    protected function canExtractWithAccessor($subject, string $propertyName, string $accessor): bool
    {
        $class = is_object($subject) ? get_class($subject) : false;
        if ($accessor === self::ACCESSOR_ARRAY) {
            return (is_array($subject) || ($subject instanceof \ArrayAccess && $subject->offsetExists($propertyName)));
        } elseif ($accessor === self::ACCESSOR_GETTER) {
            return ($class !== false && method_exists($subject, 'get' . ucfirst($propertyName)));
        } elseif ($accessor === self::ACCESSOR_ASSERTER) {
            return ($class !== false && $this->isExtractableThroughAsserter($subject, $propertyName));
        } elseif ($accessor === self::ACCESSOR_PUBLICPROPERTY) {
            return ($class !== false && isset($subject->$propertyName));
        }
        return false;
    }

    /**
     * @param mixed $subject
     * @param string $propertyName
     * @param string|null $accessor
     * @return mixed
     */
    protected function extractWithAccessor($subject, string $propertyName, ?string $accessor)
    {
        if (($accessor === self::ACCESSOR_ARRAY && is_array($subject) && isset($subject[$propertyName]))
            || ($subject instanceof \ArrayAccess && $subject->offsetExists($propertyName))
        ) {
            return $subject[$propertyName];
        } elseif (is_object($subject)) {
            if ($accessor === self::ACCESSOR_GETTER) {
                return call_user_func_array([$subject, 'get' . ucfirst($propertyName)], []);
            } elseif ($accessor === self::ACCESSOR_ASSERTER) {
                return $this->extractThroughAsserter($subject, $propertyName);
            } elseif ($accessor === self::ACCESSOR_PUBLICPROPERTY && isset($subject->$propertyName)) {
                return $subject->$propertyName;
            }
        }
        return null;
    }

    /**
     * Detect which type of accessor to use when extracting
     * $propertyName from $subject.
     *
     * @param mixed $subject
     * @param string $propertyName
     * @return string|null
     */
    protected function detectAccessor($subject, string $propertyName): ?string
    {
        if (is_array($subject) || $subject instanceof \ArrayAccess) {
            return self::ACCESSOR_ARRAY;
        }
        if (is_object($subject)) {
            $upperCasePropertyName = ucfirst($propertyName);
            $getter = 'get' . $upperCasePropertyName;
            if (is_callable([$subject, $getter])) {
                return self::ACCESSOR_GETTER;
            }
            if ($this->isExtractableThroughAsserter($subject, $propertyName)) {
                return self::ACCESSOR_ASSERTER;
            }
            if (property_exists($subject, $propertyName)) {
                return self::ACCESSOR_PUBLICPROPERTY;
            }
        }

        return null;
    }

    /**
     * Tests whether a property can be extracted through `is*` or `has*` methods.
     *
     * @param mixed $subject
     * @param string $propertyName
     * @return bool
     */
    protected function isExtractableThroughAsserter($subject, string $propertyName): bool
    {
        return method_exists($subject, 'is' . ucfirst($propertyName))
            || method_exists($subject, 'has' . ucfirst($propertyName));
    }

    /**
     * Extracts a property through `is*` or `has*` methods.
     *
     * @param object $subject
     * @param string $propertyName
     * @return mixed
     */
    protected function extractThroughAsserter(object $subject, string $propertyName)
    {
        if (method_exists($subject, 'is' . ucfirst($propertyName))) {
            return call_user_func_array([$subject, 'is' . ucfirst($propertyName)], []);
        }

        return call_user_func_array([$subject, 'has' . ucfirst($propertyName)], []);
    }
}
