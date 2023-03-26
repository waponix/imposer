<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Rule\Rule;
use Waponix\Imposer\Attribute\Directive;

#[Directive(id: 'isString')]
class IsString extends Rule
{
    public function assert(mixed $data): ?bool
    {
        return is_string($data);
    }
}