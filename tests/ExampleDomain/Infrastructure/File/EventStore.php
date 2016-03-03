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

    public function find(EventStream\Name $name, AggregateRootId $id): EventStream
    {
        $events = [];

        foreach ($this->all($name) as $event) {
            if ($event->getAggreagateRootId() == $id) {
                $events[] = $event;
            }
        }

        return new EventStream($name, $events);
    }

    public function all(EventStream\Name $name): EventStream
    {
        if (! file_exists($this->eventStorePath)) {
            return new EventStream($name, []);
        }

        $jsonEvents = json_decode(file_get_contents($this->eventStorePath), true);
        if (! isset($jsonEvents[(string) $name])) {
            return new EventStream($name, []);
        }
        $events = $this->hydrateToEventObjects($jsonEvents[(string) $name]);

        return new EventStream($name, $events);
    }

    public function apply(EventStream\Name $name, array $events)
    {
        if (! isset($this->uncommitedStreams[(string) $name])) {
            $this->uncommitedStreams[(string) $name] = $events;
            return;
        }

        $this->uncommitedStreams[(string) $name] = array_merge(
            $this->uncommitedStreams[(string) $name],
            $events
        );
    }

    public function findUncommited(EventStream\Name $name): EventStream
    {
        $events = [];
        if (isset($this->uncommitedStreams[(string) $name])) {
            $events = $this->uncommitedStreams[(string) $name];
        }

        return new EventStream($name, $events);
    }

    public function commit()
    {
        $eventStreams = [];
        if (file_exists($this->eventStorePath)) {
            $eventStreams = json_decode(file_get_contents($this->eventStorePath), true);
        }
        foreach ($this->uncommitedStreams as $streamName => $events) {
            if (isset($eventStreams[$streamName])) {
                $eventStreams[$streamName] = array_merge($eventStreams[$streamName], $events);
            } else {
                $eventStreams[$streamName] = $events;
            }
        }
        file_put_contents($this->eventStorePath, json_encode($eventStreams));

        $this->uncommitedStreams = [];
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

