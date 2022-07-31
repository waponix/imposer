<?php
require_once 'autoload.php';

use src\Imposer;
use src\Rule\Common\IsRequired;
use src\Rule\Common\IsString;
use src\Rule\Common\IsNotEmpty;
use src\Rule\Common\IsNumeric;
use src\Rule\Common\MaxLength;
use src\Rule\Common\WithinRange;
use src\Rule\Common\IsValue;

$imposer = new Imposer();
$imposer
    ->setSchema([
        'users' => [
            '[]' => [
                'name' => [
                    IsRequired::impose(),
                    IsString::impose(),
                    IsNotEmpty::impose(),
                    MaxLength::impose(['limit' => 10])
                ],
                'age' => [
                    IsRequired::impose(),
                    IsNumeric::impose(),
                    WithinRange::impose(['min' => 21, 'max' => 60])
                ],
                'gender' => [
                    IsRequired::impose(),
                    IsValue::impose(['choices' => ['male', 'female', 'other']])
                ]
            ]
        ]
    ])
    ->setInput([
        'users' => [
            [
                'name' => 'eric bermejo reyes'
            ],
            [
                'name' => 'hoshi faith agbay',
                'age' => 18
            ]
        ]
    ]);

echo '<pre>';
echo json_encode($imposer
    ->validate()
    ->getErrors(), JSON_PRETTY_PRINT);
echo '</pre>';