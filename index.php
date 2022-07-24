<?php
require_once 'autoload.php';

use src\Validator\Validator;
use src\Validator\Rule\Common\IsRequired;
use src\Validator\Rule\Common\IsString;
use src\Validator\Rule\Common\IsNotEmpty;
use src\Validator\Rule\Common\IsNumeric;
use src\Validator\Rule\Common\MaxLength;
use src\Validator\Rule\Common\WithinRange;
use src\Validator\Rule\Common\IsValue;

$validator = new Validator();
$validator
    ->setSchema([
        'user' => [
            'name' => [
                IsString::impose(),
                IsNotEmpty::impose(),
                MaxLength::impose(['limit' => 10])
            ],
            'age' => [
                IsNumeric::impose(),
                IsNotEmpty::impose(),
                WithinRange::impose(['min' => 21, 'max' => 60])
            ],
            'gender' => [
                IsRequired::impose(),
                IsValue::impose(['choices' => ['male', 'female', 'other']])
            ]
        ]
    ])
    ->setInput([
        'user' => [
            'name' => 'John Doe',
            'age' => 21
        ]
    ]);

echo '<pre>';
echo json_encode($validator
    ->validate()
    ->getErrors(), JSON_PRETTY_PRINT);
echo '</pre>';