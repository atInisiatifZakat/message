<?php

declare(strict_types=1);

namespace Inisiatif\Package\Message;

use Webmozart\Assert\Assert;
use Inisiatif\Package\Message\Contracts\MessageInterface;

final class Message implements MessageInterface
{
    /**
     * @var string
     */
    private $topicName;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $from;

    /**
     * @var array
     */
    private $body;

    private function __construct(string $topicName, string $id, string $from, array $body)
    {
        $this->id = $id;
        $this->from = $from;
        $this->body = $body;
        $this->topicName = $topicName;
    }

    public static function fromArray(string $topicName, string $id, string $from, array $body): MessageInterface
    {
        Assert::uuid($id);

        return new self($topicName, $id, $from, $body);
    }

    public static function fromJson(string $topicName, string $id, string $from, string $json): MessageInterface
    {
        Assert::uuid($id);

        $body = json_decode($json, true);

        return new self($topicName, $id, $from, $body);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function from(): string
    {
        return $this->from;
    }

    public function topic(): string
    {
        return $this->topicName;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'body' => $this->body,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
