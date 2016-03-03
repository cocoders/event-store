<?php

namespace Tests;

use ExampleDomain\Invoice;
use League\Tactician\CommandBus;
use PHPUnit_Framework_TestCase;

use Cocoders\EventStore\EventBus\EventBus;
use Cocoders\EventStore\EventStream;
use Cocoders\EventStore\EventBus\EventSubscribers;
use Cocoders\ReadModel\ProjectionManager;

use ExampleDomain\CommandBus\EventStoreMiddleware;
use ExampleDomain\CommandBus\ExecuteUseCaseWithResponderMiddleware;
use ExampleDomain\EventStore\Invoices;
use ExampleDomain\Infrastructure\File\EventStore;
use ExampleDomain\ReadModel\Json\WhoIssuedHowManyInvoicesReportProjection;
use ExampleDomain\UseCase\IssueInvoice;
use ExampleDomain\UseCase\IssueInvoice\Command as IssueInvoiceCommand;

use League\Tactician\Plugins\LockingMiddleware;

class IntegrationWithExampleDomainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var Invoices
     */
    private $invoices;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * SetUp Dependencies for test
     */
    public function setUp()
    {
        $this->eventStore = new EventStore(__DIR__.DIRECTORY_SEPARATOR.'event-store.json');
        $this->invoices = new Invoices($this->eventStore);

        $issueInvoice = new IssueInvoice($this->invoices);
        $executeUseCaseHandler = new ExecuteUseCaseWithResponderMiddleware();
        $executeUseCaseHandler->registerUseCase(
            IssueInvoiceCommand::class,
            $issueInvoice,
            new IssueInvoice\CallbackResponder(
                function () { throw new \LogicException('Info is invalid for issue invoice'); },
                function () {}
            )
        );

        $eventSubscribers = new EventSubscribers();
        $eventBus = new EventBus($eventSubscribers);
        $projectionManager = new ProjectionManager($eventSubscribers);
        $projectionManager->registerProjection(
            'InvoiceIssued',
            new WhoIssuedHowManyInvoicesReportProjection(
                __DIR__.DIRECTORY_SEPARATOR.'who-issued-how-many-invoices-report.json'
            )
        );

        $eventSourceMiddleware = new EventStoreMiddleware(
            $this->eventStore,
            $eventBus,
            [
                Invoice::class
            ]
        );
        $lockingMiddleware = new LockingMiddleware();
        $this->commandBus = new CommandBus([
            $lockingMiddleware,
            $eventSourceMiddleware,
            $executeUseCaseHandler
        ]);
    }

    /**
     * @test
     */
    public function should_store_events_after_issuing_invoice()
    {
        $command = new IssueInvoiceCommand();
        $command->sellerName = 'Cocoders Sp. z.o.o';
        $command->sellerCity = 'Toruń';
        $command->sellerPostalCode = '87-100';
        $command->sellerCountry = 'Poland';
        $command->sellerTaxIdNumber = '9562307984';
        $command->sellerStreet = 'Królowej Jadwigi 1/3';
        $command->buyerName = 'Leszek Prabucki';
        $command->buyerCity = 'Gdańsk';
        $command->buyerPostalCode = '80-283';
        $command->buyerCountry = 'Poland';
        $command->buyerTaxIdNumber = '5932455641';
        $command->buyerStreet = 'Królewskie Wzgórze 21/9';
        $command->maxItemNumber = 3;

        $this->commandBus->handle($command);

        $this->assertCount(0, $this->eventStore->findUncommited(new EventStream\Name(Invoice::class)));
        $this->assertCount(1, $this->eventStore->all(new EventStream\Name(Invoice::class)));
    }

    /**
     * @test
     */
    public function should_store_events_in_order_with_valid_data()
    {
        $command = new IssueInvoiceCommand();
        $command->sellerName = 'Cocoders Sp. z.o.o';
        $command->sellerCity = 'Toruń';
        $command->sellerPostalCode = '87-100';
        $command->sellerCountry = 'Poland';
        $command->sellerTaxIdNumber = '9562307984';
        $command->sellerStreet = 'Królowej Jadwigi 1/3';
        $command->buyerName = 'Leszek Prabucki';
        $command->buyerCity = 'Gdańsk';
        $command->buyerPostalCode = '80-283';
        $command->buyerCountry = 'Poland';
        $command->buyerTaxIdNumber = '5932455641';
        $command->buyerStreet = 'Królewskie Wzgórze 21/9';
        $command->maxItemNumber = 3;

        $this->commandBus->handle($command);

        $command = new IssueInvoiceCommand();
        $command->sellerName = 'Cocoders Sp. z.o.o';
        $command->sellerCity = 'Toruń';
        $command->sellerPostalCode = '87-100';
        $command->sellerCountry = 'Poland';
        $command->sellerTaxIdNumber = '9562307984';
        $command->sellerStreet = 'Królowej Jadwigi 1/3';
        $command->buyerName = 'Jan Kowalski';
        $command->buyerCity = 'Gdańsk';
        $command->buyerPostalCode = '80-283';
        $command->buyerCountry = 'Poland';
        $command->buyerTaxIdNumber = '5932455641';
        $command->buyerStreet = 'Kowalskiego 1';
        $command->maxItemNumber = 2;

        $this->commandBus->handle($command);

        $eventStream = $this->eventStore->all(new EventStream\Name(Invoice::class));
        $events = $eventStream->all();
        $this->assertCount(2, $eventStream);
        $this->assertEquals('Leszek Prabucki', $events[0]->getBuyer()->getName());
        $this->assertEquals('Jan Kowalski', $events[1]->getBuyer()->getName());
    }

    /**
     * @test
     */
    public function should_update_read_model_using_projection()
    {
        $command = new IssueInvoiceCommand();
        $command->sellerName = 'Cocoders Sp. z o.o.';
        $command->sellerCity = 'Toruń';
        $command->sellerPostalCode = '87-100';
        $command->sellerCountry = 'Poland';
        $command->sellerTaxIdNumber = '9562307984';
        $command->sellerStreet = 'Królowej Jadwigi 1/3';
        $command->buyerName = 'Leszek Prabucki';
        $command->buyerCity = 'Gdańsk';
        $command->buyerPostalCode = '80-283';
        $command->buyerCountry = 'Poland';
        $command->buyerTaxIdNumber = '5932455641';
        $command->buyerStreet = 'Królewskie Wzgórze 21/9';
        $command->maxItemNumber = 3;

        $this->commandBus->handle($command);

        $command = new IssueInvoiceCommand();
        $command->sellerName = 'Cocoders Sp. z.o.o';
        $command->sellerCity = 'Toruń';
        $command->sellerPostalCode = '87-100';
        $command->sellerCountry = 'Poland';
        $command->sellerTaxIdNumber = '9562307984';
        $command->sellerStreet = 'Królowej Jadwigi 1/3';
        $command->buyerName = 'Leszek Prabucki';
        $command->buyerCity = 'Gdańsk';
        $command->buyerPostalCode = '80-283';
        $command->buyerCountry = 'Poland';
        $command->buyerTaxIdNumber = '5932455641';
        $command->buyerStreet = 'Królewskie Wzgórze 21/9';
        $command->maxItemNumber = 2;

        $this->commandBus->handle($command);

        $whoIssuedHowManyInvoicesReportArray = json_decode(file_get_contents(
            __DIR__.DIRECTORY_SEPARATOR.'who-issued-how-many-invoices-report.json'
        ), true);

        $this->assertEquals(
            2,
            $whoIssuedHowManyInvoicesReportArray['9562307984']['issuedInvoices']
        );
        $this->assertEquals(
            'Cocoders Sp. z o.o.',
            $whoIssuedHowManyInvoicesReportArray['9562307984']['name']
        );
    }

    public function tearDown()
    {
        unlink(__DIR__.DIRECTORY_SEPARATOR.'event-store.json');
        unlink(__DIR__.DIRECTORY_SEPARATOR.'who-issued-how-many-invoices-report.json');
    }
}

