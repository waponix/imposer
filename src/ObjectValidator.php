<?php
namespace Waponix\Imposer;

use Waponix\Imposer\Attribute\Impose;

class ObjectValidator extends Validator
{
    private array $imposed = [];
    private readonly string $root;

    public function __construct(
        private readonly object $object,
        protected readonly array $directives
    )
    {
        $this->mapObjectRules();
    }

    private function mapObjectRules(): void
    {
        $reflectionClass = new \ReflectionClass($this->object);

        $this->root = lcfirst($reflectionClass->getName());

        $properties = $this->loadProperties($reflectionClass);

        foreach ($properties as $property) {
            $this->getImposedRules($property);
        }
    }

    private function loadProperties(\ReflectionClass $reflectionClass): array
    {
        $properties = [];

        do {
            $reflectionProperties = $reflectionClass->getProperties();

            foreach ($reflectionProperties as $property) {
                if (isset($properties[$property->getName()])) continue; //prioritize child property definition
                $properties[$property->getName()] = $property;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return $properties;
    }

    private function getImposedRules(\Reflector $reflection): void
    {
        $imposed = $this->getImposed($reflection);

        if ($imposed === null) return;

        $target = $reflection->getName();

        $this->rules[implode('.', [$this->root, $target])] = $imposed->rules;
    }

    private function getImposed(\Reflector $reflection): ?Impose
    {
        static $target = '';

        if ($target === '') {
            $target = $this->root;
        }

        $target .= '.' . $reflection->getName();
        

        if (isset($this->imposed[$target])) {
            return $this->imposed[$target];
        }

        $attribute = $reflection->getAttributes(Impose::class, 2);
        $attribute = array_pop($attribute);

        if ($attribute === null) {
            $target = $this->root;
            return null;
        } 

        $this->imposed[$target] = $imposed = $attribute->newInstance();

        $target = $this->root;

        return $imposed;
    }

    public function apply(array $data): void
    {
        
    }
}