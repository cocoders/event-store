<?php declare(strict_types=1);

namespace Cocoders\EventStore;

interface AggregateRoot
{
    public static function reconstructFrom(AggregateRootId $id, EventStream $eventStream);
    public function getRecordedEvents(): array;
    public function apply(Event $event);
}

