<?php

declare(strict_types=1);

namespace Inisiatif\Package\Message\Concerns;

use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpTopic;
use Interop\Queue\Exception;
use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpConnectionFactory;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\Exception\InvalidDestinationException;
use Inisiatif\Package\Message\Contracts\MessageInterface;

trait CreateProducer
{
    abstract public function getConnection(): AmqpConnectionFactory;

    /**
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function send(MessageInterface $message): void
    {
        $topicName = $message->topic();

        /** @var AmqpContext $context */
        $context = $this->getConnection()->createContext();

        $context->createProducer()->send(
            $this->createTopic($context, $topicName), $context->createMessage($message->toJson())
        );
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress UndefinedInterfaceMethod
     * @psalm-suppress ArgumentTypeCoercion
     */
    protected function createTopic(AmqpContext $context, string $topicName): AmqpTopic
    {
        $topic = $context->createTopic($topicName);

        $topic->setType(AmqpTopic::TYPE_FANOUT);
        $topic->setFlags(AmqpQueue::FLAG_DURABLE);

        $context->declareTopic($topic);

        return $topic;
    }
}
