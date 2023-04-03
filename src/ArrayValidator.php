<?php
namespace Waponix\Imposer;

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
}