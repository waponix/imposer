<?php
namespace Waponix\Imposer;

class ArrayValidator extends Validator
{
    const SEPARATOR = ';';

    private array $rules = [];

    public function __construct(
        private readonly array $data,
        private readonly array $directives
    )
    {
        
    }

    public function impose(string $id, string $rules): ArrayValidator
    {
        if (isset($this->rules[$id])) {
            $rules = implode(self::SEPARATOR, array_unique(array_merge(explode(self::SEPARATOR, $this->rules[$id]), explode(self::SEPARATOR, $rules))));
        }

        $this->rules[$id] = $rules;

        return $this;
    }
}