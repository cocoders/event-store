<?php declare(strict_types=1);

namespace Cocoders\EventStore\EventBus;

use Cocoders\EventStore\EventStream;

final class EventBus
{
    /**
     * @var EventSubscribers
     */
    private $subscribers;

    public function __construct(EventSubscribers $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    public function notify(EventStream $eventStream)
    {
        foreach ($eventStream as $event) {
            $this->subscribers->notify($event);
        }
    }
}
