<?php

namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Attribute\Directive;
use Waponix\Imposer\_ValueNotFound;
use Waponix\Imposer\Rule\RequireValidator;

#[Directive]
class RequireRule extends RequireValidator
{
    #[Directive(
        id: 'require',
        message: 'value is required'
    )]
    public static function require(mixed $data): bool
    {
        return !$data instanceof _ValueNotFound;
    }

    #[Directive(
        id: 'requireWhen',
        message: 'value is required'
    )]
    // is required when two values are the same
    public static function requireWhen(mixed $data, array $args): bool
    {
        return $args['$1'] === $args['$2'];
    }

    #[Directive(
        id: 'requireWhenNot',
        message: 'value is required'
    )]
    // is requred when two values are not the same
    public static function requireWhenNot(mixed $data, array $args): bool
    {
        return $args['$1'] !== $args['$2'];
    }

    #[Directive(
        id: 'requireWhenExist',
        message: 'value is required'
    )]
    // is required when a specific targeted index in the data is found
    public static function requireWhenExist(mixed $data, array $args): bool
    {
        return !$args['$1'] instanceof _ValueNotFound;
    }

    #[Directive(
        id: 'requireWhenNotExist',
        message: 'value is required'
    )]
    // is required when a specific targeted index in the data is not found
    public static function requireWhenNotExist(mixed $data, array $args): bool
    {
        return $args['$1'] instanceof _ValueNotFound;
    }
}