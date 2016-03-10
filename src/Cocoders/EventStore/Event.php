<?php declare(strict_types=1);

namespace Cocoders\EventStore;

use DateTimeInterface;

interface Event
{
    public function getAggreagateRootId(): AggregateRootId;
    public function getName(): string;
    public function occurredOn(): DateTimeInterface;
}
