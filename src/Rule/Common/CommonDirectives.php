<?php
namespace Waponix\Imposer\Rule\Common;

class CommonDirectives
{
    private array $directives = [];

    public function __construct(
        RequireRule $requireRule,
        StringRule $stringRule,
        NumberRule $numberRule,
        EmailRule $emailRule,
        EvalRule $evalRule,
    )
    {
        $this->directives = func_get_args();
    }

    public function &getDirectives(): array
    {
        return $this->directives;
    }
}