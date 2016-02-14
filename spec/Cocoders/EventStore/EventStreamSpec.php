<?php

namespace spec\Cocoders\EventStore;

use Cocoders\EventStore\Event;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EventStreamSpec extends ObjectBehavior
{
    function let(Event $firstEvent, Event $secondEvent)
    {
        $this->beConstructedWith([$firstEvent, $secondEvent]);
    }

    function it_allows_to_iterate_events(Event $firstEvent, Event $secondEvent)
    {
        $this->shouldHaveType(\Iterator::class);

        $this->current()->shouldBe($firstEvent);
        $this->next();
        $this->current()->shouldBe($secondEvent);
        $this->rewind();
        $this->current()->shouldBe($firstEvent);
    }

    function it_allows_to_get_all_events(Event $firstEvent, Event $secondEvent)
    {
        $this->all()->shouldBe([$firstEvent, $secondEvent]);
    }
}
