<?php
namespace Waponix\Imposer\Attribute;

use Waponix\Pocket\Attribute\ServiceAttribute;

/**
 * RuleMeta extends the service attribute class and exposes a different set of option
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Directive extends ServiceAttribute
{
    const ID = 'directive';

    public function __construct(
        public readonly string $message = '',
        private readonly null|string|array $tag = null,
        public readonly ?string $id = null,
        public readonly ?string $group = null
    ) {
        $tags = [self::ID];
        if (is_string($tag)) {
            $tag = array_push($tags, $tag);
        } else if (is_array($tag)) {
            $tags = array_merge($tags, $tag);
        }

        parent::__construct(tag: $tags);
    }
}