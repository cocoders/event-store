<?php declare(strict_types=1);

namespace ExampleDomain\Invoice;

use Cocoders\EventStore\AggregateRootId;
use Ramsey\Uuid\Uuid;

final class Id implements AggregateRootId
{
    private $id;

    private function __construct($id)
    {
        $this->id = $id;
    }

    public static function generate(): Id
    {
        return new Id(Uuid::uuid4()->toString());
    }

    public static function fromString(string $uuid)
    {
        if (! Uuid::isValid($uuid)) {
            throw new \InvalidArgumentException(sprintf('%s is not valid uuid', $uuid));
        }

        return new Id(Uuid::fromString($uuid)->toString());
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}

