<?php
namespace src\Validator\Rule\Common;

use src\Validator\Rule\Rule;
use src\Validator\Validator;

class IsString extends Rule
{
    /**
     * @var array
     */
    protected array $parameters = [
        self::KEY_MESSAGE => ':id should contain a string value'
    ];

    /**
     * @param $value
     * @param string $id
     * @return bool
     * @throws \src\Validator\Exception\UndefinedValidationException
     */
    public function validate($value, string $id): bool
    {
        if ($value === Validator::FIELD_NOT_EXIST) return true;

        $this->translateMessage(['id' => $id]);

        if (is_string($value) === false) return false;

        return true;
    }
}