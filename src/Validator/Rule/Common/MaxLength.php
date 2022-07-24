<?php
namespace src\Validator\Rule\Common;

use src\Validator\Rule\Rule;
use src\Validator\Utility\Parameter;
use src\Validator\Validator;

class MaxLength extends Rule
{

    protected function configure()
    {
        $this->defineParameter(self::KEY_MESSAGE, Parameter::TYPE_STRING, ':id length should not be greater than :limit');
    }

    /**
     * @param $value
     * @param string $id
     * @return bool
     * @throws \src\Validator\Utility\Exception\ParameterTypeMismatchException
     */
    public function validate($value, string $id): bool
    {
        if ($value === Validator::FIELD_NOT_EXIST) return true;

        $limit = $this->get('limit');
        $this->translateMessage(['id' => $id, 'limit' => $limit]);

        if (strlen($value) > $limit) return false;

        return true;
    }
}