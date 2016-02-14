<?php declare(strict_types=1);

namespace ExampleDomain;

use ExampleDomain\Invoice\InvoiceNotFound;

interface Invoices
{
    /**
     * @param Invoice\Id $id
     * @return Invoice
     * @throws InvoiceNotFound - when invoice with given id cannot be found
     */
    public function get(Invoice\Id $id): Invoice;
    public function add(Invoice $invoice);
}

