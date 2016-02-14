<?php declare(strict_types=1);

namespace ExampleDomain\UseCase;

use ExampleDomain\Invoice;
use ExampleDomain\Invoices;

class IssueInvoice
{
    /**
     * @var Invoices
     */
    private $invoices;

    public function __construct(Invoices $invoices)
    {
        $this->invoices = $invoices;
    }

    public function execute(IssueInvoice\Command $command, IssueInvoice\Responder $responder)
    {
        try {
            $id = Invoice\Id::generate();
            $invoice = Invoice::issueInvoice(
                $id,
                $command->getSeller(),
                $command->getBuyer(),
                $command->maxItemNumber
            );
        } catch (\InvalidArgumentException $e) {
            $responder->informationGivenForIssueInvoiceAreInvalid();
            return;
        }

        $this->invoices->add($invoice);
        $responder->invoiceIssued($id);
    }
}

