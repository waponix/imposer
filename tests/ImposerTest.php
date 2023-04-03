<?php
include_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Waponix\Pocket\Pocket;
use Waponix\Imposer\Imposer;
use Waponix\Imposer\Exception\NoRuleDefinitionException;

class ImposerTest extends TestCase
{
    private array $data = [
        'user' => [
            'name' => 'Bob',
            'email' => 'bob@email.com',
            'age' => 20
        ]
    ];
    private function loadImposer(): Imposer
    {
        $pocket = new Pocket(root: __DIR__ . '/../src');
        $imposer = $pocket->get(Imposer::class);

        return $imposer;
    }

    public function testShouldBeInstanceOfImposer()
    {
        $imposer = $this->loadImposer();

        $this->assertInstanceOf(Imposer::class, $imposer);
    }

    public function testShouldBeAbleToTranslateMessage()
    {
        $imposer = $this->loadImposer();

        $validator = $imposer->createFromArray($this->data);
        $validator
            ->impose([
                'user.name' => 'string|notEmpty|length(2)'
            ])
            ->validate();

        $error = $validator->getErrors();

        $this->assertIsArray($error);
        $this->assertSame($error['user']['name'][0], 'value is longer than 2');
    }

    public function testShouldThrowNoRuleDefinitionException()
    {
        $this->expectException(NoRuleDefinitionException::class);

        $imposer = $this->loadImposer();

        $validator = $imposer->createFromArray($this->data);
        $validator
            ->impose([
                'user.name' => 'strings.string|notEmpty|length(2)'
            ])
            ->validate();
    }
}