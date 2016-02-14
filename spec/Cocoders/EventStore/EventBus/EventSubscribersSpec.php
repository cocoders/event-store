<?php

namespace spec\Cocoders\EventStore\EventBus;

use Cocoders\EventStore\Event;
use Cocoders\EventStore\EventBus;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EventSubscribersSpec extends ObjectBehavior
{
    function it_allows_to_notify_subscribers_for_given_events(
        EventBus\EventSubscriber $subcriber1,
        EventBus\EventSubscriber $subcriber2,
        EventBus\EventSubscriber $subcriber3,
        Event $event
    ) {
        $event->getName()->willReturn('OtherEventOccured');
        $this->registerSubscriber('SomeEventOccured', $subcriber1);
        $this->registerSubscriber('OtherEventOccured', $subcriber2);
        $this->registerSubscriber('OtherEventOccured', $subcriber3);

        $this->notify($event);

        $subcriber2->notify($event)->shouldHaveBeenCalled();
        $subcriber3->notify($event)->shouldHaveBeenCalled();
    }

    function it_working_with_not_registered_event_as_well(
        Event $event
    ) {
        $event->getName()->willReturn('OtherEventOccured');

        $this->notify($event);
    }
}
