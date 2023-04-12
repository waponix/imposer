<?php
namespace Waponix\Imposer\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Impose
{
    public string $target;
    
    public function __construct(
        public readonly string $rules = '',
        public readonly ?string $getter = null,
        public readonly ?string $setter = null
    )
    {

    }
}