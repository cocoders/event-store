<?php

namespace spec\Cocoders\ReadModel;

use Cocoders\EventStore\Event;
use Cocoders\EventStore\EventBus\EventSubscribers;
use Cocoders\EventStore\EventStream;
use Cocoders\ReadModel\Projection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProjectionManagerSpec extends ObjectBehavior
{
    function let(EventSubscribers $subscribers)
    {
        $this->beConstructedWith($subscribers);

    }

    function it_allows_to_register_given_projections_as_event_subscribers(
        Projection $projection1,
        Projection $projection2,
        EventSubscribers $subscribers
    ) {
        $this->registerProjection('EventNameOccurred', $projection1);
        $this->registerProjection('EventNameOccurred', $projection2);

        $subscribers->registerSubscriber('EventNameOccurred', $projection1)->shouldHaveBeenCalled();
        $subscribers->registerSubscriber('EventNameOccurred', $projection2)->shouldHaveBeenCalled();
    }

    function it_allows_to_reload_all_registered_projections(
        Projection $projection1,
        Projection $projection2,
        EventSubscribers $subscribers,
        Event $event,
        Event $secondEvent
    ) {
        $subscribers->registerSubscriber(Argument::cetera())->willReturn();
        $subscribers->notify($event)->shouldBeCalled();
        $subscribers->notify($secondEvent)->shouldBeCalled();

        $this->registerProjection('EventNameOccurred', $projection1);
        $this->registerProjection('EventNameOccurred', $projection2);

        $event->getName()->willReturn('EventNameOccurred');
        $secondEvent->getName()->willReturn('OtherEventOccurred');
        $this->reload(new EventStream([$event->getWrappedObject(), $secondEvent->getWrappedObject()]));

        $projection1->clear()->shouldHaveBeenCalled();
        $projection2->clear()->shouldHaveBeenCalled();
    }
}
