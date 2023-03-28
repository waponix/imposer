<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Rule\Rule;
use Waponix\Imposer\Attribute\Directive;

#[Directive(
    id: 'notEmpty',
    message: 'value is empty'
)]
class NotEmpty extends Rule
{
    public function assert(mixed $data, ?array $args = null): ?bool
    {
        if (!is_string($data)) return true;
        return is_string($data) && trim($data) !== '';
    }
}