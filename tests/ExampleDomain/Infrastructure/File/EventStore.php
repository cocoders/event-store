<?php declare(strict_types=1);

namespace ExampleDomain\Infrastructure\File;

use Cocoders\EventStore\AggregateRootId;
use Cocoders\EventStore\Event;
use Cocoders\EventStore\EventStore as EventStoreInterface;
use Cocoders\EventStore\EventStream;

final class EventStore implements EventStoreInterface
{
    /**
     * @var string
     */
    private $eventStorePath;
    /**
     * @var EventStream[]
     */
    private $uncommitedStreams = [];

    public function __construct($eventStorePath)
    {
        $this->eventStorePath = $eventStorePath;
    }

    public function find(AggregateRootId $id): EventStream
    {
        $events = [];

        foreach ($this->all() as $event) {
            if ($event->getAggreagateRootId() == $id) {
                $events[] = $event;
            }
        }

        return new EventStream($events);
    }

    public function all(): EventStream
    {
        if (! file_exists($this->eventStorePath)) {
            return new EventStream([]);
        }

        $jsonEvents = json_decode(file_get_contents($this->eventStorePath), true);
        $events = $this->hydrateToEventObjects($jsonEvents);

        return new EventStream($events);
    }

    public function apply(EventStream $eventStream)
    {
        $this->uncommitedStreams[] = $eventStream;
    }

    public function findUncommited(): EventStream
    {
        $events = $this->getEventsFromUncommitedStreams([]);

        return new EventStream($events);
    }

    public function commit()
    {
        $actualEventStream = $this->all();
        $events = $actualEventStream->all();

        $events = $this->getEventsFromUncommitedStreams($events);
        $this->uncommitedStreams = [];

        file_put_contents($this->eventStorePath, json_encode($events));
    }

    /**
     * @param Event[] $events
     * @return Event[]
     */
    private function getEventsFromUncommitedStreams($events)
    {
        foreach ($this->uncommitedStreams as $stream) {
            $events = array_merge($events, $stream->all());
        }

        return $events;
    }

    private function hydrateToEventObjects(array $jsonEvents)
    {
        $events = [];
        foreach ($jsonEvents as $jsonEvent) {
            if (! isset($jsonEvent['className'])) {
                throw new \LogicException('Class name for serialized event have to be set');
            }

            $events[] = call_user_func_array([$jsonEvent['className'], 'fromJson'], [$jsonEvent]);
        }

        return $events;
    }
}

