<?php

declare(strict_types=1);

namespace Inisiatif\Package\Message\Contracts;

interface MessageInterface
{
    public function id(): string;

    public function from(): string;

    public function topic(): string;

    public function body(): array;

    public function toArray(): array;

    public function toJson(): string;
}
