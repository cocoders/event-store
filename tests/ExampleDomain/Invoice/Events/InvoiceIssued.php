<?php declare(strict_types=1);

namespace ExampleDomain\Invoice\Events;

use Cocoders\EventStore\AggregateRootId;
use DateTimeInterface;
use ExampleDomain\Invoice\Address;
use ExampleDomain\Invoice\BankAccount;
use ExampleDomain\Invoice\Buyer;
use ExampleDomain\Invoice\Id;
use ExampleDomain\Invoice\Seller;
use ExampleDomain\Invoice\TaxIdNumber;

final class InvoiceIssued implements Event
{
    /**
     * @var Id
     */
    private $id;
    private $seller;
    private $buyer;
    private $occurredOn;
    private $maxItemNumbers;

    public static function fromJson(array $jsonArray): Event
    {
        $event = new InvoiceIssued(
            Id::fromString($jsonArray['aggregateRootId']),
            new Seller(
                $jsonArray['seller']['name'],
                new TaxIdNumber($jsonArray['seller']['taxIdNumber']),
                new Address(
                    $jsonArray['seller']['street'],
                    $jsonArray['seller']['postalCode'],
                    $jsonArray['seller']['city'],
                    $jsonArray['seller']['country']
                ),
                new BankAccount(
                    $jsonArray['seller']['bankName'],
                    $jsonArray['seller']['bankNumber'],
                    $jsonArray['seller']['bicCode']
                )
            ),
            new Buyer(
                $jsonArray['buyer']['name'],
                new TaxIdNumber($jsonArray['buyer']['taxIdNumber']),
                new Address(
                    $jsonArray['buyer']['street'],
                    $jsonArray['buyer']['postalCode'],
                    $jsonArray['buyer']['city'],
                    $jsonArray['buyer']['country']
                ),
                new BankAccount(
                    $jsonArray['buyer']['bankName'],
                    $jsonArray['buyer']['bankNumber'],
                    $jsonArray['buyer']['bicCode']
                )
            ),
            (int) $jsonArray['maxItemNumbers']
        );
        $event->occurredOn = new \DateTimeImmutable($jsonArray['occuredOn']);

        return $event;
    }

    public function __construct(Id $id, Seller $seller, Buyer $buyer, $maxItemNumbers)
    {
        $this->id = $id;
        $this->seller = $seller;
        $this->buyer = $buyer;
        $this->occurredOn = new \DateTimeImmutable();
        $this->maxItemNumbers = $maxItemNumbers;
    }

    public function getAggreagateRootId(): AggregateRootId
    {
        return $this->id;
    }

    public function getMaxItemNumbers()
    {
        return $this->maxItemNumbers;
    }

    public function getName(): string
    {
        return 'InvoiceIssued';
    }

    public function occurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    public function getSeller(): Seller
    {
        return $this->seller;
    }

    public function getBuyer(): Buyer
    {
        return $this->buyer;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize(): array
    {
        $sellerAddress = $this->seller->getAddress();
        $sellerBankAccount = $this->seller->getBankAccount();
        $buyerAddress = $this->buyer->getAddress();
        $buyerBankAccount = $this->buyer->getBankAccount();

        return [
            'name' => $this->getName(),
            'className' => self::class,
            'occuredOn' => $this->occurredOn->format(DATE_ISO8601),
            'aggregateRootId' => (string) $this->getAggreagateRootId(),
            'maxItemNumbers' => $this->maxItemNumbers,
            'seller' => [
                'name' => $this->seller->getName(),
                'city' => $sellerAddress->getCity(),
                'country' => $sellerAddress->getCountry(),
                'postalCode' => $sellerAddress->getPostalCode(),
                'street' => $sellerAddress->getStreet(),
                'taxIdNumber' => (string) $this->seller->getTaxIdNumber(),
                'bankName' => $sellerBankAccount->getBankName(),
                'bankNumber' => $sellerBankAccount->getBankNumber(),
                'bicCode' => $sellerBankAccount->getBicCode()
            ],
            'buyer' => [
                'name' => $this->buyer->getName(),
                'city' => $buyerAddress->getCity(),
                'country' => $buyerAddress->getCountry(),
                'postalCode' => $buyerAddress->getPostalCode(),
                'street' => $buyerAddress->getStreet(),
                'taxIdNumber' => (string) $this->buyer->getTaxIdNumber(),
                'bankName' => $buyerBankAccount->getBankName(),
                'bankNumber' => $buyerBankAccount->getBankNumber(),
                'bicCode' => $buyerBankAccount->getBicCode()
            ],
        ];
    }
}

