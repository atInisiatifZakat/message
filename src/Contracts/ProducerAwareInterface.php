<?php

declare(strict_types=1);

namespace Inisiatif\Package\Message\Contracts;

interface ProducerAwareInterface
{
    /**
     * @return mixed
     */
    public function send(MessageInterface $message);
}
