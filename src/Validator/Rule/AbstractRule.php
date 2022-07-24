<?php
namespace src\Validator\Rule;

use src\Validator\Utility\Parameter;

/**
 * Class AbstractRule
 * @package src\Validator\Rule
 */
abstract class AbstractRule implements RuleInterface
{
    const KEY_MESSAGE = 'message';
    const KEY_TARGET_ID = 'targetId';
    const KEY_TARGET_ACTUAL_VALUE = 'targetActualValue';
    const KEY_CHOICES = 'choices';
    const KEY_RULE = 'rule';

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * @var array
     */
    protected array $parameterDefinitions = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param $id
     * @param $value
     * @return AbstractRule
     */
    public function set($id, $value): AbstractRule
    {
        Parameter::extend($this->parameters, [$id => $value]);

        return $this;
    }

    /**
     * @param $id
     * @return mixed|null
     * @throws \src\Validator\Utility\Exception\ParameterTypeMismatchException
     */
    public function get($id)
    {
        $value = isset($this->parameters[$id]) ? $this->parameters[$id] : null;
        if (isset($this->parameterDefinitions[$id]) && isset($this->parameters[$id])) {
            Parameter::validateType($id, $value, $this->parameterDefinitions[$id]);
        }
        return $value;
    }

    /**
     * @return string
     * @throws \src\Validator\Utility\Exception\ParameterTypeMismatchException
     */
    public function getMessage(): string
    {
        return $this->get(self::KEY_MESSAGE);
    }

    /**
     * @return string|null
     * @throws \src\Validator\Utility\Exception\ParameterTypeMismatchException
     */
    public function getTargetId(): ?string
    {
       return $this->get(self::KEY_TARGET_ID);
    }

    /**
     *
     */
    public function getTargetActualValue()
    {
        return $this->get(self::KEY_TARGET_ACTUAL_VALUE);
    }

    /**
     * @param $value
     * @return AbstractRule
     */
    public function passTargetActualValue($value): AbstractRule
    {
        $this->set(self::KEY_TARGET_ACTUAL_VALUE, $value);
        return $this;
    }

    /**
     * @param array $translations
     * @return AbstractRule
     * @throws \src\Validator\Utility\Exception\ParameterTypeMismatchException
     */
    protected function translateMessage(array $translations): AbstractRule
    {
        $message = $this->get(self::KEY_MESSAGE);

        foreach ($translations as $placeholder => $value) {
            $message = str_replace(':' . $placeholder, $value, $message);
        }

        $this->set(self::KEY_MESSAGE, $message);

        return $this;
    }

    /**
     * @param string $id
     * @param $types
     * @param null $default
     * @return $this
     */
    protected function defineParameter(string $id, $types, $default = null)
    {
        if (!is_array($types)) {
            $types = [$types];
        }

        $this->parameterDefinitions[$id] = $types;
        $this->set($id, $default);

        return $this;
    }

    /**
     * @return $this
     */
    private function initParameterDefinitions()
    {
        $this
            ->defineParameter(self::KEY_MESSAGE, Parameter::TYPE_STRING, '')
            ->defineParameter(self::KEY_TARGET_ID, [Parameter::TYPE_STRING, Parameter::TYPE_NULL])
            ->defineParameter(self::KEY_CHOICES, Parameter::TYPE_ARRAY)
            ->defineParameter(self::KEY_RULE, AbstractRule::class);
        return $this;
    }


    protected function configure()
    {

    }

    /**
     * @return $this
     */
    protected function init()
    {
        $this->initParameterDefinitions();
        return $this;
    }

    /**
     * @param array $parameters
     * @return mixed
     */
    abstract public static function impose(array $parameters = []);

    /**
     * @param $value
     * @param string $id
     * @return bool
     */
    abstract public function validate($value, string $id): bool;
}