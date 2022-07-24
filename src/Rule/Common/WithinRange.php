<?php
namespace src\Rule\Common;

use src\Rule\Rule;
use src\Imposer;

class WithinRange extends Rule
{
    public function validate($value, string $id): bool
    {
        if ($value === Imposer::FIELD_NOT_EXIST) return true;

        $min = $this->get('min');
        $max = $this->get('max');

        if ($min !== null & $max !== null && ($value < $min || $value > $max)) {
            $this->set(self::KEY_MESSAGE, $id . ' should only be between ' . $min . ' and ' . $max);
            return false;
        }

        if ($min !== null && $value < $min) {
            $this->set(self::KEY_MESSAGE, $id . ' should not be lower than ' . $min);
            return false;
        }

        if ($max !== null && $value > $max) {
            $this->set(self::KEY_MESSAGE, $id . ' should not be greater than ' . $max);
            return false;
        }

        return true;
    }
}