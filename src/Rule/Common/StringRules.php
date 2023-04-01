<?php

namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Attribute\Directive;

#[Directive]
class StringRules
{
    #[Directive(
        id: 'string',
        message: 'value is not a string'
    )]
    public static function isString(mixed $data): bool
    {
        return is_string($data);
    }

    #[Directive(
        id: 'notEmpty',
        message: 'value is empty'
    )]
    public static function notEmpty(mixed $data): bool
    {
        return trim($data) !== '';
    }

    #[Directive(
        id: 'length',
        message: 'value is longer than $1'
    )]
    public static function withinLength(mixed $data, array $args): bool
    {
        return strlen($data) <= $args['$1'];
    }
}