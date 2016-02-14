<?php declare(strict_types=1);

namespace ExampleDomain\CommandBus;

use Cocoders\EventStore\EventBus\EventBus;
use Cocoders\EventStore\EventStore;

use League\Tactician\Middleware;

final class EventStoreMiddleware implements Middleware
{
    /**
     * @var EventStore
     */
    private $eventStore;
    /**
     * @var EventBus
     */
    private $eventBus;

    public function __construct(EventStore $eventStore, EventBus $eventBus)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        $next($command);

        $newEvents = $this->eventStore->findUncommited();
        $this->eventStore->commit();
        $this->eventBus->notify($newEvents);
    }
}

