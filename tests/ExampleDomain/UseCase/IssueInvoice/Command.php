<?php declare(strict_types=1);

namespace ExampleDomain\UseCase\IssueInvoice;

use ExampleDomain\Invoice\BankAccount;
use ExampleDomain\Invoice\Buyer;
use ExampleDomain\Invoice\Seller;
use ExampleDomain\Invoice\TaxIdNumber;
use ExampleDomain\Invoice\Address;

final class Command
{
    public $sellerName;
    public $sellerTaxIdNumber;
    public $sellerPostalCode = '';
    public $sellerStreet = '';
    public $sellerCity = '';
    public $sellerCountry = '';
    public $sellerBankName = '';
    public $sellerBankNumber = '';
    public $sellerBicCode = '';
    public $buyerName;
    public $buyerTaxIdNumber = '';
    public $buyerPostalCode = '';
    public $buyerStreet = '';
    public $buyerCity = '';
    public $buyerCountry = '';
    public $buyerBankName = '';
    public $buyerBankNumber = '';
    public $buyerBicCode = '';
    public $maxItemNumber = 3;

    /**
     * @return Seller
     * @throws \InvalidArgumentException when some of required field is empty or some field is invalid
     */
    public function getSeller(): Seller
    {
        return new Seller(
            $this->sellerName,
            new TaxIdNumber($this->sellerTaxIdNumber),
            new Address(
                $this->sellerStreet,
                $this->sellerPostalCode,
                $this->sellerCity,
                $this->sellerCountry
            ),
            new BankAccount(
                $this->sellerBankName,
                $this->sellerBankNumber,
                $this->sellerBicCode
            )
        );
    }

    /**
     * @return Buyer
     * @throws \InvalidArgumentException when some of required field is empty or some field is invalid
     */
    public function getBuyer(): Buyer
    {
        return new Buyer(
            $this->buyerName,
            new TaxIdNumber($this->buyerTaxIdNumber),
            new Address(
                $this->buyerStreet,
                $this->buyerPostalCode,
                $this->buyerCity,
                $this->buyerCountry
            ),
            new BankAccount(
                $this->buyerBankName,
                $this->buyerBankNumber,
                $this->buyerBicCode
            )
        );
    }
}

