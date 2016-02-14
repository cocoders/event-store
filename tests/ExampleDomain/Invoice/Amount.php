<?php declare(strict_types=1);

namespace ExampleDomain\Invoice;

final class Amount
{
    private $netAmount;
    private $vatRate;

    public function __construct($netAmount, $vatRate)
    {
        $this->netAmount = $netAmount * 100;
        $this->vatRate = $vatRate * 100;
    }

    public function toGross()
    {
        return ($this->toNet() + $this->getVat());
    }

    public function getVat()
    {
        return $this->toNet() * $this->getVatRate();
    }

    public function toNet()
    {
        return $this->netAmount / 100;
    }

    public function getVatRate()
    {
        return $this->vatRate / 100;
    }
}
