<?php

declare(strict_types=1);

namespace Inisiatif\Package\Message;

use Enqueue\AmqpLib\AmqpConnectionFactory;

final class AmqpFactory
{
    public static function makeFromConfig(array $config): AmqpConnectionFactory
    {
        return new AmqpConnectionFactory($config);
    }
}
