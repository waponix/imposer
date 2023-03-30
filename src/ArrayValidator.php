<?php
namespace Waponix\Imposer;

class ArrayValidator extends Validator
{
    public function impose(array $rules): ArrayValidator
    {
        foreach ($rules as $target => $rule) {
            if (isset($this->rules[$target])) {
                $this->rules[$target] = array_unique([...explode(self::D_SEPARATOR, $this->rules[$target]), ...explode(self::D_SEPARATOR, $rule)]);
            }

            $this->rules[$target] = implode(self::D_SEPARATOR, $rules);

        }

        return $this;
    }


    public function validate(): ArrayValidator
    {
        $rules = [];
        foreach ($this->rules as $target => $raw) {
            $rules = [...$rules, ...$this->parseStringRule($raw)];

            foreach ($rules as $rule) {
                if (!isset($this->directives[$rule['id']])) continue; // TODO: this should throw exception

                $directive = $this->directives[$rule['id']];

                if ($directive->assert($this->getTargetData($target), $rule['parameters']) === false) {
                    $this->addError($target, $directive->message);
                }
            }
        }

        return $this;
    }
}