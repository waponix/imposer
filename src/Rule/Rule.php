<?php
namespace Waponix\Imposer\Rule;

use Waponix\Imposer\Interface\RuleInterface;

abstract class Rule implements RuleInterface
{
    public function __construct(
        public readonly string $id,
        public string $message = ''
    )
    {
    }

    public function assert(mixed $data, ?array $args = null): ?bool
    {
        // override this function when extended
        return null;
    }
}