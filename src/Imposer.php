<?php
namespace Waponix\Imposer;

use Waponix\Imposer\Rule\Common\CommonDirectives;
use Waponix\Imposer\Rule\Rule;
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
        private readonly ?array $directives,
    )
    {
    }

    private function &getDirectives()
    {
        $directives = [];

        foreach ($this->commonDirectives->getDirectives() as $commonDirective) {
            $directives[$commonDirective->id] = $commonDirective;
        }

        if ($this->directives !== null) {
            foreach ($this->directives as $directive) {
                $directives[$directive->id] = $directive;
            }
        }

        return $directives;
    }

    public function createFromArray(array $data): ArrayValidator
    {
        return new ArrayValidator(data: $data, directives: $this->getDirectives());
    }
}