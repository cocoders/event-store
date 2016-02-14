<?php

namespace spec\Cocoders\EventStore\EventBus;

use Cocoders\EventStore\Event;
use Cocoders\EventStore\EventBus;
use Cocoders\EventStore\EventBus\EventSubscriber;
use Cocoders\EventStore\EventStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EventBusSpec extends ObjectBehavior
{
    function let(
        EventSubscriber $someEventOccuredSubcriber,
        EventSubscriber $otherEventOccuredSubcriber
    ) {
        $eventSubcribers = new EventBus\EventSubscribers();
        $eventSubcribers->registerSubscriber('SomeEventOccured', $someEventOccuredSubcriber->getWrappedObject());
        $eventSubcribers->registerSubscriber('OtherEventOccured', $otherEventOccuredSubcriber->getWrappedObject());

        $this->beConstructedWith($eventSubcribers);
    }

    function it_notify_subcribers_registered_for_given_event(Event $event, EventSubscriber $someEventOccuredSubcriber)
    {
        $event->getName()->willReturn('SomeEventOccured');

        $this->notify(new EventStream([$event->getWrappedObject()]));

        $someEventOccuredSubcriber->notify($event)->shouldHaveBeenCalled();
    }
}
