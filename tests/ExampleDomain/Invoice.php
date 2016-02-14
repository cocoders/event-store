<?php declare(strict_types=1);

namespace ExampleDomain;

use Cocoders\EventStore\AggregateRoot;
use Cocoders\EventStore\AggregateRootBehavior;
use ExampleDomain\Invoice\Amount;
use ExampleDomain\Invoice\Events\InvoiceIssued;
use ExampleDomain\Invoice\Events\ItemAdded;

final class Invoice implements AggregateRoot
{
    use AggregateRootBehavior;

    /**
     * @var Invoice\Id
     */
    private $id;
    private $maxItemNumbers;
    private $itemNumbers = 0;

    private function __construct(Invoice\Id $id)
    {
        $this->id = $id;
    }

    public static function issueInvoice(
        Invoice\Id $id,
        Invoice\Seller $seller,
        Invoice\Buyer $buyer,
        int $maxItemNumbers = 3
    ): Invoice {
        $invoice = new Invoice($id);

        $invoiceIssued = new InvoiceIssued($id, $seller, $buyer, $maxItemNumbers);
        $invoice->events[] = $invoiceIssued;
        $invoice->applyInvoiceIssued($invoiceIssued);

        return $invoice;
    }

    public function addItem($name, Amount $amount, $quantity)
    {
        if ($this->itemNumbers >= $this->maxItemNumbers) {
            throw new \LogicException(
                sprintf('Max item number is %d, so you cannot add next item', $this->maxItemNumbers)
            );
        }
        $itemAdded = new ItemAdded($this->id, $name, $amount, $quantity);
        $this->events[] = $itemAdded;
        $this->applyItemAdded($itemAdded);
    }

    private function applyInvoiceIssued(InvoiceIssued $invoiceIssued)
    {
        $this->maxItemNumbers = $invoiceIssued->getMaxItemNumbers();
    }

    private function applyItemAdded(ItemAdded $itemAdded)
    {
        $this->itemNumbers++;
    }
}

