<?php
namespace Waponix\Imposer;

abstract class Validator
{
    const D_SEPARATOR = ';';
    const T_SEPARATOR = '.';
    const ID_ARRAY = '[]';

    protected array $errors = [];
    protected array $rules = [];

    public function __construct(
        protected readonly array $data,
        protected readonly array $directives
    )
    {
        
    }

    abstract public function impose(array $rules): Validator;

    protected function parseStringRule(string $src): array
    {
        $ruleId = null;
        $parameters = [];
        $parameterValue = '';
        $rules = [];

        $tokens = \PhpToken::tokenize('<?php ' . $src);

        foreach ($tokens as $token) {
            $tokenName = $token->getTokenName();

            if ($tokenName === 'T_STRING' && $ruleId === null) {
                $ruleId = $token->text;
                continue;
            }

            if ($ruleId === null) {
                continue;
            }

            if (in_array($tokenName, ['(', ')', 'T_WHITESPACE'])) {
                continue;
            }

            if ($tokenName === ',' && $parameterValue !== '') {
                $parameters[] = $parameterValue;
                $parameterValue = '';
                continue;
            }

            if ($tokenName === '|' && $ruleId !== null) {
                if ($parameterValue !== '') {
                    $parameters[] = $this->translateValue($parameterValue);
                }

                $rules[] = [
                    'id' => $ruleId,
                    'parameters' => $parameters
                ];
                $parameterValue = '';
                $ruleId = null;
                $parameters = [];

                continue;
            }

            if ($tokenName === 'T_LNUMBER') {
                $parameterValue = (integer) $token->text;
            } else if ($tokenName === 'T_DNUMBER') {
                $parameterValue = (float) $token->text;
            } else {
                $parameterValue .= $token->text;
            }
        }

        if ($parameterValue !== '') {
            $parameters[] = $this->translateValue($parameterValue);
        }

        if ($ruleId !== null) {
            $rules[] = [
                'id' => $ruleId,
                'parameters' => $parameters
            ];
            $ruleId = null;
        }

        return $rules;
    }

    protected function translateValue(mixed $value): mixed
    {
        if (!is_string($value)) return $value;
        if (substr($value, 0, 1) !== '[' || substr($value, -1) !== ']') return $value;

        $value = substr($value, 1, strlen($value) - 2);

        return $this->getTargetData($value);
    }

    protected function getTargetData(string $target)
    {
        $keys = explode('.', $target);
        $targetData = $this->data;

        foreach ($keys as $key) {
            if (!isset($targetData[$key])) {
                return null;
            }

            if ($targetData === null) {
                $targetData = $this->data[$key];
            }

            $targetData = $targetData[$key];
        }

        return $targetData;
    }

    protected function addError(string $target, string $message): Validator
    {
        $keys = explode('.', $target);

        $errors = &$this->errors;
        foreach ($keys as $key) {
            if (!isset($errors[$key])) $errors[$key] = [];
            $errors = &$errors[$key];
        }

        $errors[] = $message;

        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}