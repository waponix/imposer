<?php
include_once __DIR__ . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Waponix\Pocket\Pocket;
use Waponix\Imposer\Imposer;

$pocket = new Pocket(root: __DIR__ . '/src');
$imposer = $pocket->get(Imposer::class);

$data = [
    'user' => [
        'name' => 0,
        'email' => 'bob@email.com',
        'age' => 20
    ]
];

$validator = $imposer->createFromArray($data);

$validator
    ->impose([
        'user.name' => 'string|notEmpty|length(1)',
        'user.address' => 'require|string',
    ])
    ->validate();

$errors = $validator->getErrors();

var_dump(json_encode($errors, JSON_PRETTY_PRINT)); die;
