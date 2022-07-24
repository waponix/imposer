<?php
namespace src\Rule\Common;

use src\Rule\Rule;
use src\Utility\Arr;
use src\Utility\Parameter;
use src\Imposer;

class IsValue extends Rule
{
    protected function configure()
    {
        $this
            ->defineParameter(self::KEY_MESSAGE, Parameter::TYPE_STRING, ':id value can only be :choices');
    }

    public function validate($value, string $id): bool
    {
        if ($value === Imposer::FIELD_NOT_EXIST) return true;

        $choices = $this->get(self::KEY_CHOICES);

        $this->translateMessage(['id' => $id, 'choices' => Arr::toWord($choices, ', ' , 'or')]);

        if (!in_array($value, $choices, true)) return false;

        return true;
    }
}