<?php declare(strict_types=1);

namespace Cocoders\EventStore;

use Iterator;

final class EventStream implements Iterator
{
    /**
     * @var array
     */
    private $events;

    public function __construct(array $events)
    {
        $this->events = new \ArrayIterator($events);
    }

    public function current(): Event
    {
        return $this->events->current();
    }

    public function next()
    {
        $this->events->next();
    }

    public function key()
    {
        return $this->events->key();
    }

    public function valid(): bool
    {
        return $this->events->valid();
    }

    public function rewind()
    {
        $this->events->rewind();
    }

    public function all(): array
    {
        $events = [];
        foreach ($this->events as $event) {
            $events[] = $event;
        }

        return $events;
    }
}
