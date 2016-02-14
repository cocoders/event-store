<?php declare(strict_types=1);

namespace ExampleDomain\Invoice;

final class TaxIdNumber
{
    private $taxIdNumber;

    public function __construct($taxIdNumber)
    {
        if (!$taxIdNumber) {
            throw new \InvalidArgumentException('Tax id number cannot be empty');
        }
        $this->taxIdNumber = $taxIdNumber;
    }

    public function __toString(): string
    {
        return (string) $this->taxIdNumber;
    }
}
