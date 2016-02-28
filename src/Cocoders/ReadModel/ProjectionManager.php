<?php declare(strict_types=1);

namespace Cocoders\ReadModel;

use Cocoders\EventStore\Event;
use Cocoders\EventStore\EventBus\EventSubscribers;
use Cocoders\EventStore\EventStream;

final class ProjectionManager
{
    private $subscribers;
    /**
     * @var Projection[]
     */
    private $projections = [];
    private $projectionsForEvent = [];

    public function __construct(EventSubscribers $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    public function registerProjection($eventName, Projection $projection)
    {
        $this->projections[] = $projection;
        $this->projectionsForEvent[$eventName][] = $projection;
        $this->subscribers->registerSubscriber($eventName, $projection);
    }

    public function reload(EventStream $eventStream)
    {
        foreach ($this->projections as $projection) {
            $projection->clear();
        }

        foreach ($eventStream as $event) {
            $this->notifyProjections($event);
        }
    }

    private function notifyProjections(Event $event)
    {
        foreach ($this->projectionsForEvent($event) as $projection) {
            $projection->notify($event);
        }
    }

    /**
     * @param Event $event
     * @return Projection[]
     */
    private function projectionsForEvent(Event $event): array
    {
        if (! isset($this->projectionsForEvent[$event->getName()])) {
            return [];
        }

        return $this->projectionsForEvent[$event->getName()];
    }
}

