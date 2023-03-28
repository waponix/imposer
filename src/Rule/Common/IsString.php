<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Rule\Rule;
use Waponix\Imposer\Attribute\Directive;

#[Directive(
    id: 'string', 
    message: 'value is not a string'
)]
class IsString extends Rule
{
    public function assert(mixed $data, ?array $args = null): ?bool
    {
        return is_string($data);
    }
}