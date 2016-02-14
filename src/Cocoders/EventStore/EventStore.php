<?php declare(strict_types=1);

namespace Cocoders\EventStore;

interface EventStore
{
    public function find(AggregateRootId $id): EventStream;
    public function findUncommited(): EventStream;
    public function all(): EventStream;
    public function apply(EventStream $eventStream);
    public function commit();
}

