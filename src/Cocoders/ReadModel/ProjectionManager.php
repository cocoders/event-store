<?php declare(strict_types=1);

namespace Cocoders\ReadModel;

use Cocoders\EventStore\EventBus\EventSubscribers;
use Cocoders\EventStore\EventStream;

final class ProjectionManager
{
    private $subscribers;
    /**
     * @var Projection[]
     */
    private $projections = [];

    public function __construct(EventSubscribers $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    public function registerProjection($eventName, Projection $projection)
    {
        $this->projections[] = $projection;
        $this->subscribers->registerSubscriber($eventName, $projection);
    }

    public function reload(EventStream $eventStream)
    {
        foreach ($this->projections as $projection) {
            $projection->clear();
        }

        foreach ($eventStream as $event) {
            $this->subscribers->notify($event);
        }
    }
}

