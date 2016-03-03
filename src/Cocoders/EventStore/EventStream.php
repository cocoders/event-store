<?php declare(strict_types=1);

namespace Cocoders\EventStore;

use Iterator;

final class EventStream implements Iterator
{
    /**
     * @var array
     */
    private $events;

    public function __construct(EventStream\Name $name, array $events)
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
        $this->events->uasort(
            function (Event $event1, Event $event2) {
                return $event1->occurredOn() <= $event2->occurredOn() ? -1 : 1;
            }
        );

        $events = [];
        foreach ($this->events as $event) {
            $events[] = $event;
        }

        return $events;
    }
}
