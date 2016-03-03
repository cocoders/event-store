<?php declare(strict_types=1);

namespace Cocoders\EventStore;

trait AggregateRootBehavior
{
    private $events = [];

    public static function reconstructFrom(AggregateRootId $id, EventStream $eventStream)
    {
        $aggregate = new static($id);

        foreach ($eventStream as $event) {
            $aggregate->apply($event);
        }

        return $aggregate;
    }

    public function getRecordedEvents(): array
    {
        return $this->events;
    }

    public function apply(Event $event)
    {
        $method = 'apply'.$event->getName();
        if (method_exists($this, $method)) {
            $this->$method($event);
        }
    }
}

