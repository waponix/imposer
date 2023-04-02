<?php
namespace Waponix\Imposer\Rule;

/**
 * Created this abstract class to solve the issue on when to evaluate non-existing values,
 * when the value is detected as _ValueNotFound, only rules that extends this abstract class will be able to evaluate the value, others will be skipped
 */
abstract class AbstractRequire
{

}