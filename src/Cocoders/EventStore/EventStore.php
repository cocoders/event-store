<?php declare(strict_types=1);

namespace Cocoders\EventStore;

interface EventStore
{
    public function find(EventStream\Name $name, AggregateRootId $id): EventStream;
    public function findUncommited(EventStream\Name $name): EventStream;
    public function all(EventStream\Name $name): EventStream;
    public function apply(EventStream\Name $name, array $events);
    public function commit();
}

