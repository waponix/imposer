<?php
namespace Waponix\Imposer\Attribute;

#[\Attribuate(\Attribuate::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class Impose
{
    public function __construct(
        public readonly string $rules = '',
        public readonly ?string $getter = null
    )
    {

    }
}