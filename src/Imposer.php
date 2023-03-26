<?php
namespace Waponix\Imposer;

use Waponix\Imposer\Rule\Common\IsString;
use Waponix\Imposer\Rule\Rule;
use Waponix\Pocket\Pocket;
use Waponix\Pocket\Attribute\Service;

#[Service(
    args: [
        'commonDirectives' => [
            IsString::class
        ],
        'directives' => '#directive'
    ]
)]
class Imposer
{
    public function __construct(
        private readonly array $commonDirectives,
        private ?array $directives,
    )
    {
    }

    private function &getDirectives()
    {
        $directives = [];
        foreach ($this->commonDirectives as $directive) {
            if (!$directive instanceof Rule) continue;
            $directives[$directive->id] = &$directive;
        }

        if ($this->directives !== null) {
            foreach ($this->directives as $directive) {
                if (!$directive instanceof Rule) continue;
                $directives[$directive->id] = &$directive;
            }
        }

        return $directives;
    }

    public function createFromArray(array $data): ArrayValidator
    {
        return new ArrayValidator(data: $data, directives: $this->getDirectives());
    }
}