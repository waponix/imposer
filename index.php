<?php
include_once __DIR__ . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Waponix\Pocket\Pocket;
use Waponix\Imposer\Imposer;
use Waponix\Imposer\Attribute\Impose;

Pocket::setRoot(__DIR__ . '/src');
$pocket = Pocket::getInstance();
$imposer = $pocket->get(Imposer::class);

$data = [
    'user' => [
        'name' => 0,
        'email' => 'bob@email.com',
        'age' => 20
    ]
];

abstract class Person
{
    #[Impose(
        rules: 'require|string|notEmpty|length(25)'
    )]
    public string $name;
    
    #[Impose(
        rules: 'require|number|min(12)'
    )]
    public int $age;
    
    #[Impose(
        rules: 'require|string|notEmpty|within(male, female)'
    )]
    public string $gender;

    #[Impose(
        rules: 'require|string|notEmpty|email',
        setter: 'setEmail'
    )]
    private string $email;


    public function setEmail(string $email): Person
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}

class User extends Person
{
}

$user = new User;

$validator = $imposer->createFromObject($user);
$validator->validate([
    'user' => [
        'name' => 'eric',
        'age' => 28,
        'gender' => 'male',
        'email' => 'eric.bermejo.reyes@gmail.com'
    ]
]);

$validator->apply();

var_dump($validator->getErrors(), $user->getEmail());