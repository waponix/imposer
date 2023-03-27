<?php
namespace Waponix\Imposer;

class ArrayValidator extends Validator
{
    const D_SEPARATOR = ';';
    const T_SEPARATOR = '.';
    const ID_ARRAY = '[]';

    private array $rules = [];
    private ?string $target = null;

    public function __construct(
        private readonly array $data,
        private readonly array $directives
    )
    {
        
    }

    public function impose(string $rules): ArrayValidator
    {
        if ($this->target === null) return $this;

        if (isset($this->rules[$this->target])) {
            $rules = implode(self::D_SEPARATOR, array_unique(array_merge(explode(self::D_SEPARATOR, $this->rules[$this->target]), explode(self::D_SEPARATOR, $rules))));
        }

        $this->rules[$this->target] = $rules;

        return $this;
    }

    public function target(string $target): ArrayValidator
    {
        $this->target = $target;
        return $this;
    }


    public function validate(): ArrayValidator
    {
        foreach ($this->rules as $raw) {
            $rule = $this->parseStringRule($raw);
        }

        return $this;
    }

    private function parseStringRule(string $src): array
    {
        $ruleId = null;
        $parameters = [];
        $isParameterName = false;
        $parameterName = '';
        $isParameterValue = false;
        $parameterValue = '';
        $rules = [];

        $tokens = token_get_all('<?php ' . $src);

        foreach ($tokens as $token) {
            $tokenName = null;
            if (is_array($token)) {
                $tokenName = token_name($token[0]);
            }

            if ($tokenName === 'T_STRING' && $ruleId === null) {
                $ruleId = $token[1];
            } else if ($ruleId === null) {
                continue;
            }
            
            if ($tokenName === 'T_VARIABLE') {
                $isParameterName = true;
            } else if ($tokenName === 'T_WHITESPACE') {
                $isParameterValue = true;
                $isParameterName = false;
            }
            
            if ($tokenName !== null && $isParameterName === true) {
                $parameterName .= $token[1];
            }

            if ($tokenName !== null && $isParameterName === false && $isParameterValue === true) {
                $parameterValue .= $token[1];
            }

            if (!is_string($token)) continue;

            if (trim($token) === ',') {
                $parameterValue = trim($parameterValue);
                if ($parameterName !== '') {
                    $parameters[$parameterName] = $parameterValue;
                } else if ($parameterValue !== '') {
                    $parameters[] = $parameterValue;
                }

                $isParameterName = false;
                $isParameterValue = false;
                $parameterName = '';
                $parameterValue = '';

                continue;
            }

            if (trim($token) === '|') {
                $parameterValue = trim($parameterValue);

                if ($parameterName !== '') {
                    $parameters[$parameterName] = $parameterValue;
                } else if ($parameterValue !== '') {
                    $parameters[] = $parameterValue;
                }

                $rules[] = [
                    'ruleId' => $ruleId,
                    'parameters' => $parameters
                ];

                $ruleId = null;
                $parameters = [];
                $isParameterName = false;
                $isParameterValue = false;
                $parameterName = '';
                $parameterValue = '';

                continue;
            }

            if ($isParameterName === true) {
                $parameterName .= $token;
                continue;
            }

            if ($isParameterValue === true) {
                $parameterValue .= $token;
            }
        }

        $parameterValue = trim($parameterValue);

        if ($parameterName !== '') {
            $parameters[$parameterName] = $parameterValue;
        } else if ($parameterValue !== '') {
            $parameters[] = $parameterValue;
        }

        $rules[] = [
            'ruleId' => $ruleId,
            'parameters' => $parameters
        ];

        return $rules;
    }
}