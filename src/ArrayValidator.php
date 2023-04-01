<?php
namespace Waponix\Imposer;

use Waponix\Imposer\Exception\NoRuleDefinitionException;

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
        $rules = [];
        foreach ($this->rules as $target => $raw) {
            $rules = [...$rules, ...$this->parseStringRule($raw)];

            foreach ($rules as $rule) {
                if (!isset($this->directives[$rule['id']])) {
                    throw new NoRuleDefinitionException('The rule ' . $rule['id'] . ' does not exist');
                }

                $directive = $this->directives[$rule['id']];

                if ($directive->assert($this->getTargetData($target), $rule['parameters']) === false) {
                    $this->addError($target, $this->translateById($directive->message, $rule['parameters']));
                }
            }
        }

        return $this;
    }
}