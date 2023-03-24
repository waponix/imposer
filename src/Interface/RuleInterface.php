<?php
namespace Waponix\Imposer\Interface;

interface RuleInterface
{
    public function parsePattern(string $pattern): RuleInterface;
    public function assert(mixed $data): bool;
}