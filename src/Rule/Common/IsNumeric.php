<?php
namespace src\Rule\Common;

use src\Rule\Rule;
use src\Imposer;

class IsNumeric extends Rule
{
    /**
     * @var array
     */
    protected array $parameters = [
        self::KEY_MESSAGE => ':id should contain a valid numeric value'
    ];

    public function validate($value, string $id): bool
    {
        if ($value === Imposer::FIELD_NOT_EXIST) return true;

        $this->translateMessage(['id' => $id]);

        if (is_numeric($value) === false) return false;

        return true;
    }
}