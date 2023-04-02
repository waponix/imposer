<?php
namespace Waponix\Imposer;

use Waponix\Imposer\Exception\NoRuleDefinitionException;
use Waponix\Imposer\Rule\RequireValidator;

class ArrayValidator extends Validator
{
    public function impose(array $rules): ArrayValidator
    {
        foreach ($rules as $target => $rule) {
            $this->rules[$target] = $rule;
        }

        return $this;
    }


    public function validate(): ArrayValidator
    {
        foreach ($this->rules as $target => $raw) {
            $data = $this->getTargetData($target);
            $rules = $this->parseStringRule($raw);

            foreach ($rules as $rule) {
                if (!isset($this->directives[$rule['id']])) {
                    throw new NoRuleDefinitionException('The rule ' . $rule['id'] . ' does not exist');
                }

                $directive = $this->directives[$rule['id']];
                $assert = $directive->assert;

                if ($data instanceof _ValueNotFound && !$directive->group instanceof RequireValidator) {
                    // skip this validation since it is not a qualified rule for validating the value _ValueNotFound
                    continue;
                }

                if ($assert($data, $rule['parameters']) === false) {
                    $this->addError($target, $this->translateById($directive->message, $rule['parameters']));
                }
            }
        }

        return $this;
    }
}