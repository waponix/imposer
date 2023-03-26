<?php
namespace Waponix\Imposer;

class ArrayValidator extends Validator
{
    const D_SEPARATOR = ';';
    const T_SEPARATOR = '.';
    const ID_ARRAY = '[]';

    private array $rules = [];
    private ?string $target = null;

    public function __construct(
        private readonly array $data,
        private readonly array $directives
    )
    {
        
    }

    public function impose(string $rules): ArrayValidator
    {
        if ($this->target === null) return $this;

        if (isset($this->rules[$this->target])) {
            $rules = implode(self::D_SEPARATOR, array_unique(array_merge(explode(self::D_SEPARATOR, $this->rules[$this->target]), explode(self::D_SEPARATOR, $rules))));
        }

        $this->rules[$this->target] = $rules;

        return $this;
    }

    public function aim(string $target): ArrayValidator
    {
        $this->target = $target;
        return $this;
    }


    public function validate(): ArrayValidator
    {
        return $this;
    }

    private function parseRule(string $src)
    {

    }
}