<?php

declare(strict_types=1);

namespace Inisiatif\Package\Message\Contracts;

use Interop\Amqp\AmqpConnectionFactory;

interface AmqpAwareInterface
{
    public function getConnection(): AmqpConnectionFactory;
}
