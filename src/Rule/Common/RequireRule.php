<?php

namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Attribute\Directive;

#[Directive]
class RequireRule
{
    #[Directive(
        id: 'require',
        message: 'value is required'
    )]
    public static function isRequired(mixed $data): void
    {
        
    }
}