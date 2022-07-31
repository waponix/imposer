<?php
namespace src\Rule;

use src\Exception\UndefinedValidationException;

/**
 * Class Rule
 * @package src\Rule
 */
class Rule extends AbstractRule
{
    /**
     * Rule constructor.
     * @param array $parameters
     */
    private function __construct(array $parameters)
    {
        $this
            ->init()
            ->configure();

        foreach($parameters as $id => $value) {
            $this->set($id, $value);
        }

        // get the initial state of the rule
        $this->initState = $this->parameters;
    }


    protected function configure()
    {
        // TODO: Implement configure() method.
    }

    /**
     * @param array $parameters
     * @return static
     */
    public static function impose(array $parameters = [])
    {
        return new static($parameters);
    }

    /**
     * @param $value
     * @param string $id
     * @return bool
     * @throws UndefinedValidationException
     */
    public function validate($value, string $id): bool
    {
        // This is to remind caller he needs to define this function
        throw new UndefinedValidationException('There is no defined validation found for class ' . get_class($this));
    }

    /**
     * @return $this
     */
    public function recycle()
    {
        $initial = md5(serialize($this->initState));
        $current = md5(serialize($this->parameters));

        if ($initial !== $current) {
            $this->parameters = $this->initState;
        }

        return $this;
    }
}