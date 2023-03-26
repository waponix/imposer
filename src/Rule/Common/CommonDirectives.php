<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Rule\Rule;
use Waponix\Pocket\Attribute\Service;

class CommonDirectives
{
    private readonly array $directives;

    public function __construct(
        NotEmpty $notEmpty,
        IsString $isString
    )
    {
        $args = func_get_args();
        
        $directives = [];
        foreach ($args as $directive) {
            if (!$directive instanceof Rule) continue;
            $directives[$directive->id] = $directive;
        }

        $this->directives = $directives;
    }

    public function getDirectives(): array
    {
        return $this->directives;
    }
}