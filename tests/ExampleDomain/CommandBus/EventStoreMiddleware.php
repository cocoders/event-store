<?php declare(strict_types=1);

namespace ExampleDomain\CommandBus;

use Cocoders\EventStore\EventBus\EventBus;
use Cocoders\EventStore\EventStore;

use Cocoders\EventStore\EventStream;
use ExampleDomain\Invoice;
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
    private $streamNames = [];

    public function __construct(EventStore $eventStore, EventBus $eventBus, array $streamNames)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
        $this->streamNames = $streamNames;
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

        $unncommitedEventStreams = [];
        foreach ($this->streamNames as $streamName) {
            $unncommitedEventStreams[] = $this->eventStore->findUncommited(
                new EventStream\Name($streamName)
            );
        }
        $this->eventStore->commit();

        foreach ($unncommitedEventStreams as $eventStream) {
            $this->eventBus->notify($eventStream);
        }
    }
}

