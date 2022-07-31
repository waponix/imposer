<?php
namespace src;

use src\Exception\InvalidRuleException;
use src\Rule\AbstractRule;

/**
 * Class Imposer
 * @package src
 */
class Imposer
{
    const FIELD_NOT_EXIST = 'FIELD_NOT_EXIST';

    const SPL_KEY_GROUP = '[]';

    /**
     * @var array
     */
    private array $schema;

    /**
     * @var array
     */
    private array $input;

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * Imposer constructor.
     * @param $schema
     * @param $input
     */
    public function __construct(array $schema = [], array $input = [])
    {
        $this->schema = $schema;
        $this->input = $input;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function setInput(array $input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * @param array $schema
     * @return $this
     */
    public function setSchema(array $schema)
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return $this
     * @throws InvalidRuleException
     */
    public function validate()
    {
        $this->validateLoop($this->schema);
        return $this;
    }

    /**
     * @param array $schema
     * @param array $id
     * @throws InvalidRuleException
     * @throws Utility\Exception\ParameterTypeMismatchException
     */
    private function validateLoop($schema = [], $id = [])
    {
        foreach ($schema as $key => $scheme) {
            $newId = $id;
            $newId[] = $key;

            if ($key === self::SPL_KEY_GROUP) {
                // perform group validation
                $this->validateGroup($scheme, $id);
                continue;
            }

            if (is_array($scheme) && !$this->scanSchemeForRules($scheme)) {
                // keep going deeper into the schema
                $this->validateLoop($scheme, $newId);
                continue;
            } else if (is_array($scheme) && $this->scanSchemeForRules($scheme)) {
                // when the array contains a rule, consider them as a rule group
                $this->executeValidations($scheme, $newId);
                continue;
            }

            if (!$scheme instanceof AbstractRule) {
                $schemeType = is_object($scheme) ? get_class($scheme) : gettype($scheme);
                throw new InvalidRuleException('Expecting rule to be an instance of ' . AbstractRule::class . ', ' . $schemeType . ' given');
            }

            $newId = implode('.', $id);
            $this->executeValidation($scheme, $newId);
        }
    }

    /**
     * @param array $schema
     * @param array $id
     * @return $this|void
     * @throws InvalidRuleException
     * @throws Utility\Exception\ParameterTypeMismatchException
     */
    private function validateGroup(array $schema, array $id)
    {
        $keys = $this->getInputKeys(implode('.', $id));

        if (empty($keys)) return;

        foreach($keys as $key) {
            $newId = $id;
            $newId[] = $key;
            $this->validateLoop($schema, $newId);
        }

        return $this;
    }

    /**
     * @param array $schema
     * @return bool
     */
    private function scanSchemeForRules(array $schema)
    {
        $hasRule = false;

        foreach ($schema as $scheme) {
            if ($scheme instanceof AbstractRule) $hasRule = true; break;
        }

        return $hasRule;
    }

    /**
     * @param AbstractRule $rule
     * @param string $id
     * @throws Utility\Exception\ParameterTypeMismatchException
     */
    private function executeValidation(AbstractRule $rule, string $id)
    {
        $value = $this->getInput($id);

        if ($rule->getTargetId() !== null) {
            $targetValue = $this->getInput($rule->getTargetId());
            $rule->passTargetActualValue($targetValue);
        }

        // perform validation;
        if ($rule->recycle()->validate($value, $id) === false) {
            $this->addError($id, $rule->getMessage());
        }
    }

    /**
     * @param array $rules
     * @param array $id
     * @return $this
     * @throws InvalidRuleException
     * @throws Utility\Exception\ParameterTypeMismatchException
     */
    private function executeValidations(array $rules, array $id)
    {
        $id = implode('.', $id);

        foreach ($rules as $rule) {
            if (!$rule instanceof AbstractRule) {
                $itemType = is_object($rule) ? get_class($rule) : ucfirst(gettype($rule));
                throw new InvalidRuleException('Expecting rule to be an instance of ' . AbstractRule::class . ', ' . $itemType . ' given');
            }

            $this->executeValidation($rule, $id);
        }
        return $this;
    }

    /**
     * @param null $id
     * @param null $input
     * @return array|mixed|string
     */
    private function getInput($id = null, $input = null)
    {
        if ($id === null || empty($id)) {
            // when there is no id provided, return the whole input
            return $this->input;
        }

        if ($input === null) {
            $input = $this->input;
        }

        $id = explode('.', $id);
        $key = array_shift($id);

        if (!isset($input[$key])) {
            // return a field not exist value for non existing keys
            return self::FIELD_NOT_EXIST;
        }

        if (count($id) > 0) {
            // keep going until the last key is reached
            return $this->getInput(implode('.', $id), $input[$key]);
        }

        return $input[$key];
    }

    /**
     * @param null $id
     * @param null $input
     * @return array
     */
    private function getInputKeys($id = null, $input = null)
    {
        $input = $this->getInput($id);

        if (!is_array($input)) return [];

        return array_keys($input);
    }

    /**
     * @param string $id
     * @param $message
     */
    private function addError(string $id, $message)
    {
        $ids = explode('.', $id);

        $key = array_shift($ids);
        $tree = &$this->errors[$key];

        while (count($ids) > 0) {
            $key = array_shift($ids);
            $tree = &$tree[$key];
        }

        $tree[] = $message;
    }
}