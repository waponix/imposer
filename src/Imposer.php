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
     */
    private function validateLoop($schema = [], $id = [])
    {
        foreach ($schema as $key => $item) {
            $newId = $id;
            $newId[] = $key;

            if (is_array($item) && !$this->scanItemsForRules($item)) {
                // keep going deeper into the schema
                $this->validateLoop($item, $newId);
                continue;
            } else if (is_array($item) && $this->scanItemsForRules($item)) {
                // when the array contains a rule, consider them as a rule group
                $this->executeValidations($item, $newId);
                continue;
            }

            if (!$item instanceof AbstractRule) {
                $itemType = is_object($item) ? get_class($item) : gettype($item);
                throw new InvalidRuleException('Expecting rule to be an instance of ' . AbstractRule::class . ', ' . $itemType . ' given');
            }

            $newId = implode('.', $id);
            $this->executeValidation($item, $newId);
        }
    }

    /**
     * @param array $items
     * @return bool
     */
    private function scanItemsForRules(array $items)
    {
        $hasRule = false;

        foreach ($items as $item) {
            if ($item instanceof AbstractRule) $hasRule = true; break;
        }

        return $hasRule;
    }

    /**
     * @param AbstractRule $rule
     * @param array $id
     */
    private function executeValidation(AbstractRule $rule, string $id)
    {
        $value = $this->getInput($id);

        if ($rule->getTargetId() !== null) {
            $targetValue = $this->getInput($rule->getTargetId());
            $rule->passTargetActualValue($targetValue);
        }

        // perform validation;
        if ($rule->validate($value, $id) === false) {
            $this->addError($id, $rule->getMessage());
        }
    }

    /**
     * @param array $rules
     * @param array $id
     * @return $this
     * @throws InvalidRuleException
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
    public function getInput($id = null, $input = null)
    {
        if ($id === null) {
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