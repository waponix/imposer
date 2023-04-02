<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Attribute\Directive;

#[Directive]
class EmailRule
{
    #[Directive(
        id: 'email',
        message: 'value is not an email'
    )]
    public static function email(mixed $data, array $args): bool
    {
        var_dump(filter_val($data, FILTER_VALIDATE_EMAIL)); die;
        return filter_val($data, FILTER_VALIDATE_EMAIL) !== false;
    }
}