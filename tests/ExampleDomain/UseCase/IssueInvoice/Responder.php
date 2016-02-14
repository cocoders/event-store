<?php declare(strict_types=1);

namespace ExampleDomain\UseCase\IssueInvoice;

use ExampleDomain\Invoice;

interface Responder
{
    public function informationGivenForIssueInvoiceAreInvalid();
    public function invoiceIssued(Invoice\Id $id);
}

