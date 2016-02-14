<?php declare(strict_types=1);

namespace Cocoders\EventStore\EventBus;

use Cocoders\EventStore\Event;

interface EventSubscriber
{
    public function notify(Event $event);
}
