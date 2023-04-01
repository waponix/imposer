<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Rule\Rule;
use Waponix\Imposer\Attribute\Directive;

#[Directive(
    id: 'length', 
    message: 'value should not be longer than $1'
)]
class MaxStringLength extends Rule
{
    public function assert(mixed $data, ?array $args = null): ?bool
    {
        return is_string($data) && strlen($data) === $args['$1'];
    }
}