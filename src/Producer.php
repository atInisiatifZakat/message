<?php

declare(strict_types=1);

namespace Inisiatif\Package\Message;

use Interop\Amqp\AmqpConnectionFactory;
use Inisiatif\Package\Message\Concerns\CreateProducer;
use Inisiatif\Package\Message\Contracts\ProducerAwareInterface;

final class Producer implements ProducerAwareInterface
{
    use CreateProducer;

    /**
     * @var AmqpConnectionFactory
     */
    private static $connection;

    private function __construct(AmqpConnectionFactory $connection)
    {
        self::$connection = $connection;
    }

    public static function make(array $config): ProducerAwareInterface
    {
        $connection = AmqpFactory::makeFromConfig($config);

        return new self($connection);
    }

    public function getConnection(): AmqpConnectionFactory
    {
        return self::$connection;
    }
}
