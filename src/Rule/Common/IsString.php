<?php
namespace src\Rule\Common;

use src\Rule\Rule;
use src\Imposer;

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
     * @throws \src\Exception\UndefinedValidationException
     */
    public function validate($value, string $id): bool
    {
        if ($value === Imposer::FIELD_NOT_EXIST) return true;

        $this->translateMessage(['id' => $id]);

        if (is_string($value) === false) return false;

        return true;
    }
}