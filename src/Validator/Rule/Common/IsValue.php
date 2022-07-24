<?php
namespace src\Validator\Rule\Common;

use src\Validator\Rule\Rule;
use src\Validator\Utility\Arr;
use src\Validator\Utility\Parameter;
use src\Validator\Validator;

class IsValue extends Rule
{
    protected function configure()
    {
        $this
            ->defineParameter(self::KEY_MESSAGE, Parameter::TYPE_STRING, ':id value can only be :choices');
    }

    public function validate($value, string $id): bool
    {
        if ($value === Validator::FIELD_NOT_EXIST) return true;

        $choices = $this->get(self::KEY_CHOICES);

        $this->translateMessage(['id' => $id, 'choices' => Arr::toWord($choices, ', ' , 'or')]);

        if (!in_array($value, $choices, true)) return false;

        return true;
    }
}