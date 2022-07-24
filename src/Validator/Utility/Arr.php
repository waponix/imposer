<?php
namespace src\Validator\Utility;

class Arr
{
    public static function toWord(array $items, string $separator = ', ', ?string $last = 'and')
    {
        $lastItem = null;
        if ($last !== null && count($items) > 1) {
            $lastItem = array_pop($items);
        } else {
            $last = '';
        }

        return implode(' ', [implode($separator, $items), $last, $lastItem]);
    }
}