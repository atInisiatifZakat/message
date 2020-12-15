<?php

declare(strict_types=1);

namespace Inisiatif\Package\Message\Console;

use Exception;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpContext;
use Illuminate\Console\Command;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Amqp\Impl\AmqpTopic;
use Enqueue\Consumption\QueueConsumer;
use Enqueue\Consumption\ChainExtension;
use Interop\Amqp\AmqpConnectionFactory;
use Inisiatif\Package\Message\AmqpFactory;
use Illuminate\Contracts\Config\Repository;
use Enqueue\Consumption\Extension\SignalExtension;
use Inisiatif\Package\Message\Contracts\AmqpAwareInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

abstract class ConsumerCommand extends Command implements AmqpAwareInterface
{
    /**
     * @var string
     */
    protected $name = 'message:consume';

    /**
     * @var string
     */
    protected $description = 'Consume message from AMQP';

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function handle(): void
    {
        /** @var AmqpContext $context */
        $context = $this->getConnection()->createContext();

        $extensions = $this->extensions();

        $consumer = new QueueConsumer($context, $extensions);

        foreach ($this->registerProcessors() as $topicName => $processor) {
            $queueName = $this->queuePrefix() . '.' . $topicName;

            $topic = $context->createTopic($topicName);
            $topic->setType(AmqpTopic::TYPE_FANOUT);
            $topic->setFlags(AmqpQueue::FLAG_DURABLE);
            $context->declareTopic($topic);

            $queue = $context->createQueue($queueName);
            $queue->setFlags(AmqpQueue::FLAG_DURABLE);
            $context->declareQueue($queue);

            $bind = new AmqpBind($topic, $queue);
            $context->bind($bind);

            $consumer->bind($queue, $this->laravel->make($processor));

            $this->info(\sprintf('Binding queue `%s` to `%s`', $queueName, $processor));
        }

        $this->line('Consume....');

        $consumer->consume();
    }

    public function getConnection(): AmqpConnectionFactory
    {
        /** @var Repository $repository */
        $repository = $this->getLaravel()->get(Repository::class);

        $config = $repository->get('services.amqp');

        return AmqpFactory::makeFromConfig($config);
    }

    protected function registerExtensions(): array
    {
        return [];
    }

    protected function extensions(): ChainExtension
    {
        $defaultExtensions = [
            new SignalExtension(),
        ];

        return new ChainExtension(
            \array_merge($defaultExtensions, $this->registerExtensions())
        );
    }

    abstract protected function queuePrefix(): string;

    abstract protected function registerProcessors(): array;
}
