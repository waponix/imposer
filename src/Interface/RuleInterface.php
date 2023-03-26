<?php
namespace Waponix\Imposer\Interface;

interface RuleInterface
{
    public function assert(mixed $data, ?array $args = null): ?bool;
}