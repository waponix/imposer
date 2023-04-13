<?php
namespace Waponix\Imposer;

use Waponix\Imposer\Attribute\Impose;

class ObjectValidator extends Validator
{
    private array $imposed = [];
    private readonly string $root;
    private \ReflectionClass $objectReflection;

    public function __construct(
        private readonly object $object,
        protected readonly array $directives
    )
    {
        $this->objectReflection = new \ReflectionClass($object);
        $this->mapObjectRules();
    }

    private function mapObjectRules(): void
    {
        $this->root = lcfirst($this->objectReflection->getName());

        $properties = $this->loadProperties($this->objectReflection);

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

    private function getReflectionProperty(\ReflectionClass $reflectionClass, string $property): ?\ReflectionProperty
    {
        do {
            if ($reflectionClass->hasProperty($property) === false) continue;

            return $reflectionClass->getProperty($property);
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return null;
    }

    private function getImposedRules(\Reflector $reflection): void
    {
        $imposed = $this->getImposed($reflection);

        if ($imposed === null) return;

        $target = $reflection->getName();
        $imposed->target = $target;

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

    public function apply(): void
    {
        if ($this->isValid() === false) return; // only apply values if there are no errors

        foreach ($this->imposed as $target => $impose) {
            if (!$impose instanceof Impose) continue;

            $data = $this->getTargetData($target);

            if ($data instanceof _ValueNotFound) continue; // data to assign is not defined, skip it

            $setter = $impose->setter;
            
            if ($setter === null) {
                // try assigning the value to the property directly
                $property = $this->getReflectionProperty($this->objectReflection, $impose->target);

                if ($property === null) continue; // property not found, skip it
                if ($property->isPublic() === false) continue; // property is not accessible, skip it (TODO: this should throw exception)

                $this->object->{$property->getName()} = $data;
            } else {
                if ($this->objectReflection->hasMethod($setter) === false) // no method found, skip it (TODO: this should throw exception)

                $method = $this->objectReflection->getMethod($setter);

                if ($method->isPublic() === false) // method is not accessible, skip it (TODO: this should throw exception)
                
                $this->object->{$setter}($data);
            }
        }
    }
}