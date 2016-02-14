<?php declare(strict_types=1);

namespace ExampleDomain\UseCase\IssueInvoice;

use ExampleDomain\Invoice;

final class CallbackResponder implements Responder
{
    /**
     * @var
     */
    private $informationGivenForIssueInvoiceAreInvalid;
    /**
     * @var
     */
    private $invoiceIssued;

    public function __construct(
        $informationGivenForIssueInvoiceAreInvalid,
        $invoiceIssued
    ) {

        $this->informationGivenForIssueInvoiceAreInvalid = $informationGivenForIssueInvoiceAreInvalid;
        $this->invoiceIssued = $invoiceIssued;
    }

    public function informationGivenForIssueInvoiceAreInvalid()
    {
        call_user_func_array($this->informationGivenForIssueInvoiceAreInvalid, []);
    }

    public function invoiceIssued(Invoice\Id $id)
    {
        call_user_func_array($this->invoiceIssued, [$id]);
    }
}

