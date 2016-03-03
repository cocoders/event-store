<?php

namespace spec\Cocoders\EventStore\EventStream;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NameSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('EventStreamName');
    }

    function it_can_be_casted_to_string()
    {
        $this->__toString()->shouldBe('EventStreamName');
    }
}
