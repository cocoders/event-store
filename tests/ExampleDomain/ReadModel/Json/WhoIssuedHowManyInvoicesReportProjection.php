<?php declare(strict_types=1);

namespace ExampleDomain\ReadModel\Json;

use Cocoders\EventStore\Event;
use Cocoders\ReadModel\Projection;
use ExampleDomain\Invoice\Events\InvoiceIssued;
use ExampleDomain\Invoice\Seller;

final class WhoIssuedHowManyInvoicesReportProjection implements Projection
{
    private $jsonReportPath;

    public function __construct($jsonReportPath)
    {
        $this->jsonReportPath = $jsonReportPath;
    }

    public function notify(Event $event)
    {
        if (! $event instanceof InvoiceIssued) {
            return;
        }

        $seller = $event->getSeller();
        $readModel = $this->loadReadModel();
        if (! isset($readModel[(string) $seller->getTaxIdNumber()])) {
            $readModel[(string) $seller->getTaxIdNumber()] = $this->initializeReadModelFor($seller);
        }
        $readModel[(string) $seller->getTaxIdNumber()]['issuedInvoices']++;

        file_put_contents($this->jsonReportPath, json_encode($readModel));
    }

    public function clear()
    {
        file_put_contents($this->jsonReportPath, '');
    }

    /**
     * @return array
     */
    private function loadReadModel()
    {
        $readModel = [];
        if (file_exists($this->jsonReportPath)) {
            $readModel = json_decode(file_get_contents($this->jsonReportPath), true);
        }

        return $readModel;
    }

    private function initializeReadModelFor(Seller $seller)
    {
        return [
            'name' => $seller->getName(),
            'taxIdNumber' => (string) $seller->getTaxIdNumber(),
            'issuedInvoices' => 0
        ];
    }
}

