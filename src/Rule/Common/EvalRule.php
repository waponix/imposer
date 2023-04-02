<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Attribute\Directive;

#[Directive]
class EvalRule
{
    #[Directive(
        id: 'within',
        message: 'value can only be $1'
    )]
    // $args is being passed by reference because it will be modified to match the message translation
    public static function within(mixed $data, array &$args): bool
    {
        $flag = in_array($data, $args, true);

        // re-assigning a value to $1 to be used in the error message translation
        $args['$1'] = implode(' or ', $args);
        
        return $flag;
    }

    #[Directive(
        id: 'notWithin',
        message: 'value can cannot be $1'
    )]
    // $args is being passed by reference because it will be modified to match the message translation
    public static function notWithin(mixed $data, array &$args): bool
    {
        $flag = !in_array($data, $args, true);

        // re-assigning a value to $1 to be used in the error message translation
        $args['$1'] = implode(' or ', $args);
        
        return $flag;
    }
}