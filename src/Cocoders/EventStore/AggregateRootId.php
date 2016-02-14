<?php declare(strict_types=1);

namespace Cocoders\EventStore;

interface AggregateRootId
{
    public function __toString(): string;
}

