<?php
namespace src\Rule\Common;

use src\Rule\Rule;
use src\Utility\Parameter;
use src\Imposer;

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
     * @throws \src\Utility\Exception\ParameterTypeMismatchException
     */
    public function validate($value, string $id): bool
    {
        $this->get('rule');
        // Prepare the error message
        $this->translateMessage(['id' => $id]);

        if ($value === Imposer::FIELD_NOT_EXIST) return false;

        return true;
    }
}