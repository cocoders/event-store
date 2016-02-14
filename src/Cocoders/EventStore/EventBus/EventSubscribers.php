<?php declare(strict_types=1);

namespace Cocoders\EventStore\EventBus;

use Cocoders\EventStore\Event;

class EventSubscribers
{
    private $subscribersByEvent = [];

    public function registerSubscriber($eventName, EventSubscriber $subscriber)
    {
        $this->subscribersByEvent[$eventName][] = $subscriber;
    }

    public function notify(Event $event)
    {
        foreach ($this->subscribersForEvent($event) as $subscriber) {
            $subscriber->notify($event);
        }
    }

    /**
     * @param Event $event
     * @return EventSubscriber[]
     */
    private function subscribersForEvent(Event $event): array
    {
        if (! isset($this->subscribersByEvent[$event->getName()])) {
            return [];
        }
        
        return $this->subscribersByEvent[$event->getName()];
    }
}
