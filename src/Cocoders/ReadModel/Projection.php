<?php declare(strict_types=1);

namespace Cocoders\ReadModel;

use Cocoders\EventStore\EventBus\EventSubscriber;

interface Projection extends EventSubscriber
{
    public function clear();
}

