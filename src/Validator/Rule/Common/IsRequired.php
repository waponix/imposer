<?php
namespace src\Validator\Rule\Common;

use src\Validator\Rule\Rule;
use src\Validator\Utility\Parameter;
use src\Validator\Validator;

class IsRequired extends Rule
{
    protected function configure()
    {
        $this->defineParameter(self::KEY_MESSAGE, Parameter::TYPE_STRING, ':id is missing');
    }

    /**
     * @param $value
     * @param string $id
     * @return bool
     * @throws \src\Validator\Utility\Exception\ParameterTypeMismatchException
     */
    public function validate($value, string $id): bool
    {
        $this->get('rule');
        // Prepare the error message
        $this->translateMessage(['id' => $id]);

        if ($value === Validator::FIELD_NOT_EXIST) return false;

        return true;
    }
}