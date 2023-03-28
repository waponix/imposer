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
        $rules = [];
        foreach ($this->rules as $raw) {
            $rules = array_merge($rules, $this->parseStringRule($raw));
        }

        var_dump($rules); die;

        return $this;
    }

    private function parseStringRule(string ...$src): array
    {
        $ruleId = null;
        $parameters = [];
        $parameterValue = '';
        $rules = [];

        $src = implode('|', $src);

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
                    $parameters[] = $parameterValue;
                }

                $rules[] = [
                    'ruleId' => $ruleId,
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

        $parameterValue = trim($parameterValue);

        if ($parameterValue !== '') {
            $parameters[] = $parameterValue;
        }

        if ($ruleId !== null) {
            $rules[] = [
                'ruleId' => $ruleId,
                'parameters' => $parameters
            ];
            $ruleId = null;
        }

        return $rules;
    }
}