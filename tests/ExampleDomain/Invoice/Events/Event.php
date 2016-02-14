<?php declare(strict_types=1);

namespace ExampleDomain\Invoice\Events;

use Cocoders\EventStore\Event as DomainEvent;
use \JsonSerializable;

interface Event extends DomainEvent, JsonSerializable
{
    public static function fromJson(array $jsonArray): Event;
}

