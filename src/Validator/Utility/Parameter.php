<?php
namespace src\Validator\Utility;

use src\Validator\Utility\Exception\ParameterTypeMismatchException;

class Parameter
{
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'double';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_ANY = 'any';
//    const TYPE_RESOURCE = 'resource';
    const TYPE_NULL = 'NULL';
//    const TYPE_UNKNOWN = 'unknown type';

    private array $parameters = [];

    private function __construct()
    {

    }

    /**
     * @param $initial
     * @param $current
     */
    public static function extend(&$initial, $current) {
        $initial = array_merge($initial, $current);
    }

    /**
     * @param string $id
     * @param $value
     * @param array $types
     * @return bool
     * @throws ParameterTypeMismatchException
     */
    public static function validateType(string $id, $value, array $types)
    {
        $valueType = ucfirst(gettype($value));
        $wordTypes = Arr::toWord(array_map('ucfirst', $types), ', ', 'or');

        foreach ($types as $type) {
            if (strtolower($type) === strtolower(self::TYPE_ANY)) return true;

            if (is_string($type) && class_exists($type, false)) {
                $valueType = is_object($value) ? get_class($value) : $valueType;
                $wordTypes = $type;

                // load the defined class type
                spl_autoload_call($type);

                if ($value instanceof $type) return true;
            }

            if (strtolower($valueType) === strtolower($type)) return true;
        }

        throw new ParameterTypeMismatchException('The parameter ' . $id . ' is expecting ' . $wordTypes . ' type, ' . $valueType . ' is given');
    }
}