<?php
namespace Waponix\Imposer\Attribute;

use Waponix\Pocket\Attribute\Service;
use Waponix\Pocket\Attribute\Factory;

/**
 * RuleMeta extends the service attribute class and exposes a different set of option
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Directive extends Service
{
    const ID = 'directive';

    public function __construct(
        private readonly string $id,
        private readonly string $message = '',
        private readonly null|string|array $tag = null,
        private readonly ?Factory $creator = null
    ) {
        $tags = [self::ID];
        if (is_string($tag)) {
            $tag = array_push($tags, $tag);
        } else if (is_array($tag)) {
            $tags = array_merge($tags, $tag);
        }

        $tags = array_unique($tags);

        $args['id'] = $id;
        $args['message'] = $message;

        parent::__construct(args:$args, tag: $tags);
    }
}