<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Rule\Rule;
use Waponix\Imposer\Attribute\Directive;

#[Directive(id: 'notEmpty')]
class NotEmpty extends Rule
{
    public function assert(mixed $data, ?array $args = null): ?bool
    {
        return is_string($data) && trim($data) !== '';
    }
}