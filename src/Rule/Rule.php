<?php
namespace Waponix\Imposer\Rule;

use Waponix\Imposer\Interface\RuleInterface;

abstract class Rule implements RuleInterface
{
    public function __construct(
        public readonly string $id
    )
    {
    }

    public function decodePattern(string $pattern): Rule
    {
        return $this;
    }

    public function assert(mixed $data): ?bool
    {
        // override this function when extended
        return null;
    }
}