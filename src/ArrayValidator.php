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
        foreach ($this->rules as $target => $raw) {
            $data = $this->getTargetData($target);

            foreach ($this->parseStringRule($raw) as $rule) {
                if (!isset($this->directives[$rule['id']])) {
                    throw new NoRuleDefinitionException('The rule ' . $rule['id'] . ' does not exist');
                }

                if ($rule['id'] !== self::REQUIRED && $data instanceof _ValueNotFound) {
                    continue; // ignore this, since it is not required
                }

                $directive = $this->directives[$rule['id']];
                $assert = $directive->assert;

                if ($rule['id'] === self::REQUIRED && $data instanceof _ValueNotFound) {
                    $this->addError($target, $this->translateById($directive->message, $rule['parameters']));
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