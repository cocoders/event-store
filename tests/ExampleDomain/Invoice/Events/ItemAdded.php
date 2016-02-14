<?php declare(strict_types=1);

namespace ExampleDomain\Invoice\Events;

use Cocoders\EventStore\AggregateRootId;
use DateTimeImmutable;
use ExampleDomain\Invoice\Amount;
use ExampleDomain\Invoice\Id;

final class ItemAdded implements Event
{
    private $id;
    /**
     * @var
     */
    private $itemServiceName;
    /**
     * @var Amount
     */
    private $amount;
    /**
     * @var
     */
    private $quantity;
    private $occurredOn;

    public static function fromJson(array $jsonArray): Event
    {
        return new ItemAdded(
            Id::fromString($jsonArray['aggregateRootId']),
            $jsonArray['serviceName'],
            new Amount(
                $jsonArray['amount']['net'],
                $jsonArray['amount']['vatRate']
            ),
            (int) $jsonArray['quantity']
        );
    }

    public function __construct(Id $id, string $name, Amount $amount, int $quantity)
    {
        $this->id = $id;
        $this->itemServiceName = $name;
        $this->amount = $amount;
        $this->quantity = $quantity;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggreagateRootId(): AggregateRootId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return 'ItemAdded';
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public  function getServiceName(): string
    {
        return $this->itemServiceName;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'className' => self::class,
            'occuredOn' => $this->occurredOn->format(DATE_ISO8601),
            'aggregateRootId' => (string) $this->getAggreagateRootId(),
            'quantity' => $this->getQuantity(),
            'serviceName' => $this->getServiceName(),
            'amount' => [
                'net' => $this->amount->toNet(),
                'vatRate' => $this->amount->getVatRate()
            ]
        ];
    }
}

