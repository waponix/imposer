<?php
namespace src\Rule\Common;

use src\Rule\Rule;

class IsNotEmpty extends Rule
{
    /**
     * @param $value
     * @param string $id
     * @return bool
     * @throws \src\Utility\Exception\ParameterTypeMismatchException
     */
    public function validate($value, string $id): bool
    {
        $this->translateMessage(['id' => $id]);

        if ($value === '') return false;

        return true;
    }
}