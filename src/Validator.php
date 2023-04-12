<?php
namespace Waponix\Imposer;

use Waponix\Imposer\Exception\NoRuleDefinitionException;
use Waponix\Imposer\Rule\RequireValidator;

abstract class Validator
{
    const D_SEPARATOR = ';';
    const T_SEPARATOR = '.';
    const ID_ARRAY = '[]';

    const REQUIRED = 'require';

    protected array $errors = [];
    protected array $data = [];
    protected array $rules = [];

    public function __construct(
        array $rules,
        protected readonly array $directives
    )
    {
        $this->rules = $rules;
    }

    public function validate(array $data): Validator
    {
        $this->data = $data;
        foreach ($this->rules as $target => $raw) {
            $data = $this->getTargetData($target);
            $rules = $this->parseStringRule($raw);

            foreach ($rules as $rule) {
                if (!isset($this->directives[$rule['id']])) {
                    throw new NoRuleDefinitionException('The rule ' . $rule['id'] . ' does not exist');
                }

                $directive = $this->directives[$rule['id']];
                $assert = $directive->assert;

                if ($data instanceof _ValueNotFound && !$directive->group instanceof RequireValidator) {
                    // skip this validation since it is not a qualified rule for validating the value _ValueNotFound
                    continue;
                }

                if ($assert($data, $rule['parameters']) === false) {
                    $this->addError($target, $this->translateById($directive->message, $rule['parameters']));
                }
            }
        }

        return $this;
    }

    protected function parseStringRule(string $src): array
    {
        $ruleId = null;
        $ruleIdHolder = '';
        $parameters = [];
        $parameterValue = '';
        $rules = [];

        $tokens = \PhpToken::tokenize('<?php ' . $src);

        foreach ($tokens as $token) {
            $tokenName = $token->getTokenName();

            // get the first potential value of rule id
            if (!in_array($tokenName, ['T_OPEN_TAG', 'T_WHITESPACE']) && $ruleIdHolder === '') {
                $ruleIdHolder = $token->text;
                continue;
            }

            // rule id reached its last value, can now pass it to $ruleId and reset $ruleIdHolder
            if (in_array($tokenName, ['|', '(']) && $ruleId === null) {
                $ruleId = $ruleIdHolder;
            }

            // keep concatinating the value to rule id (ignore white spaces)
            if ($tokenName !== 'T_WHITESPACE' && $ruleIdHolder !== '' && $ruleId === null) {
                $ruleIdHolder .= $token->text;
            }

            // rule id is not identified yet so don't proceed with parsing other parts
            if ($ruleId === null) {
                continue;
            }

            if (in_array($tokenName, ['(', ')', 'T_WHITESPACE'])) {
                continue;
            }

            // push parameter value to the parameters stack
            if ($tokenName === ',' && $parameterValue !== '') {
                $this->pushParameter($parameters, $this->translateValue($parameterValue));
                $parameterValue = '';
                continue;
            }

            // push the rule to the stack
            if ($tokenName === '|' && $ruleId !== null) {
                if ($parameterValue !== '') {
                    $this->pushParameter($parameters, $this->translateValue($parameterValue));
                    $parameterValue = '';
                }

                $rules[] = [
                    'id' => $ruleId,
                    'parameters' => $parameters
                ];
                $ruleId = null;
                $ruleIdHolder = '';
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

        // process the last possible rule value that were parsed
        $ruleId = $ruleIdHolder;
        if ($parameterValue !== '') {
            $this->pushParameter($parameters, $this->translateValue($parameterValue));
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

    private function pushParameter(array &$array, mixed $value): void
    {
        $id = count($array) + 1;
        $array['$' . $id] = $value;
    }

    private function translateValue(mixed $value): mixed
    {
        if (!is_string($value)) return $value;
        if (substr($value, 0, 1) !== '[' || substr($value, -1) !== ']') return $value;

        $value = substr($value, 1, strlen($value) - 2);

        return $this->getTargetData($value);
    }

    protected function getTargetData(string $target): mixed
    {
        $keys = explode('.', $target);
        $targetData = $this->data;

        foreach ($keys as $key) {
            if (!isset($targetData[$key])) {
                return new _ValueNotFound;
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

    protected function translateById(string $subject, array $translations): string
    {
        foreach ($translations as $id => $value) {
            $subject = str_replace($id, $value, $subject);
        }

        return $subject;
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