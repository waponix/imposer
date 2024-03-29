<?php
namespace Waponix\Imposer;

use Waponix\Imposer\Rule\Common\CommonDirectives;
use Waponix\Imposer\Rule\Rules;
use Waponix\Imposer\Attribute\Directive;
use Waponix\Pocket\Attribute\Service;

#[Service(
    args: [
        'directives' => '#directive'
    ]
)]
class Imposer
{
    public function __construct(
        private readonly CommonDirectives $commonDirectives,
        private readonly array $directives,
    )
    {
    }

    private function getDirectives()
    {
        return [
            ...$this->loadFromCollection($this->commonDirectives->getDirectives()),
            ...$this->loadFromCollection($this->directives)
        ];
    }

    private function loadFromCollection(?array $collection): array
    {
        $directives = [];
        if ($collection === null) return $directives;

        foreach ($collection as $directive) {
            $reflectionClass = new \ReflectionClass($directive);
            $classDirective = $this->getDirective($reflectionClass);

            $group = $classDirective->group ?? null;

            $methods = $reflectionClass->getMethods();

            foreach ($methods as $method) {
                $methodDirective = $this->getDirective($method);

                $id = match ($group) {
                    null => $methodDirective->id,
                    $group => implode('.', [$group, $methodDirective->id])
                };

                $directives[$id] = (object) [
                    'group' => $directive,
                    'assert' => function (mixed $data, &$args) use ($directive, $method) {
                        $methodArgs = [$data, &$args]; // ensures that the args are passed by reference
                        return $method->invokeArgs($directive, $methodArgs);
                    },
                    'message' => $methodDirective->message,
                ];
            }
        }

        return $directives;
    }

    private function getDirective(\ReflectionClass | \ReflectionMethod $reflection): ?Directive
    {
        $attributes = $reflection->getAttributes(Directive::class);
        $attribute = array_pop($attributes);

        if ($attribute === null) return null;

        return $attribute->newInstance();
    }

    public function createFromArray(array $rules): ArrayValidator
    {
        return new ArrayValidator(rules: $rules, directives: $this->getDirectives());
    }

    public function createFromObject(object $data): ObjectValidator
    {
        return new ObjectValidator($data, directives: $this->getDirectives());
    }
}