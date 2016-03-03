<?php declare(strict_types=1);

namespace ExampleDomain\EventStore;

use Cocoders\EventStore\EventStore;
use Cocoders\EventStore\EventStream;
use ExampleDomain\Invoice;
use ExampleDomain\Invoices as InvoicesInterface;

final class Invoices implements InvoicesInterface
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function get(Invoice\Id $id): Invoice
    {
        $events = $this->eventStore->find(
            new EventStream\Name(Invoice::class),
            $id)
        ;

        return Invoice::reconstructFrom($id, $events);
    }

    public function add(Invoice $invoice)
    {
        $this->eventStore->apply(
            new EventStream\Name(Invoice::class),
            $invoice->getRecordedEvents()
        );
    }
}

