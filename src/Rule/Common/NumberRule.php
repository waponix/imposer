<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Attribute\Directive;

#[Directive]
class NumberRule
{
    #[Directive(
        id: 'number',
        message: 'value is not a number',
    )]
    public static function isNumber(mixed $data): bool 
    {
        return is_numeric($data);
    }

    #[Directive(
        id: 'min',
        message: 'value is lower than $1'
    )]
    public static function min(mixed $data, array $args): bool
    {
        return $data >= $args['$1'];
    }

    #[Directive(
        id: 'max',
        message: 'value is higher than $1'
    )]
    public static function max(mixed $data, array $args): bool
    {
        return $data <= $args['$1'];
    }

    #[Directive(
        id: 'withinRange',
        message: 'value is not within range of $1 to $2'
    )]
    public static function inRange(mixed $data, array $args): bool
    {
        return $data >= $args['$1'] && $data <= $args['$2'];
    }

    #[Directive(
        id: 'notWithinRange',
        message: 'value is within range of $1 to $2'
    )]
    public static function outRange(mixed $data, array $args): bool
    {
        return $data <= $args['$1'] && $data >= $args['$2'];
    }
}