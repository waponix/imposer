<?php
namespace src\Rule;

interface RuleInterface
{
    public function getName(): string;
    public function set($id, $value);
    public function get($id);
    public function getMessage(): string;
    public function validate($value, string $id): bool;
    public function recycle();
    public function getTargetId(): ?string;
    public function getTargetActualValue();
    public function passTargetActualValue($value);
    public static function impose(array $parameters = []);
}