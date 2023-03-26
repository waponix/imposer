<?php
namespace Waponix\Imposer\Interface;

interface RuleInterface
{
    public function decodePattern(string $pattern): RuleInterface;
    public function assert(mixed $data): ?bool;
}