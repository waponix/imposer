<?php
namespace Waponix\Imposer\Rule\Common;

use Waponix\Imposer\Rule\Rule;
use Waponix\Pocket\Attribute\Service;

class CommonDirectives
{
    private array $directives = [];

    public function __construct(
        StringRules $stringRules,
    )
    {
        $this->directives = func_get_args();
    }

    public function &getDirectives(): array
    {
        return $this->directives;
    }
}