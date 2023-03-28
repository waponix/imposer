<?php
namespace Waponix\Imposer;

class ArrayValidator extends Validator
{
    public function impose(string ...$rules): ArrayValidator
    {
        if ($this->target === null) return $this;

        $rules = implode('|', $rules);

        if (isset($this->rules[$this->target])) {
            $rules = implode(self::D_SEPARATOR, array_unique(array_merge(explode(self::D_SEPARATOR, $this->rules[$this->target]), explode(self::D_SEPARATOR, $rules))));
        }

        $this->rules[$this->target] = $rules;

        return $this;
    }

    public function target(string $target): ArrayValidator
    {
        $this->target = $target;
        return $this;
    }


    public function validate(): ArrayValidator
    {
        $rules = [];
        foreach ($this->rules as $target => $raw) {
            $rules = array_merge($rules, $this->parseStringRule($raw));

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