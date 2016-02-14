Quite basic and simple Event Store abstraction layer
==============================================

[![Travis build](https://api.travis-ci.org/cocoders/event-store.svg)](https://travis-ci.org/cocoders/event-store)

Event store layer to help us - [Cocoders](http://cocoders.com) to implement event sourcing in our projects.
It is quite simple (or event stupid) abstraction layer (yunno [KISS](https://en.wikipedia.org/wiki/KISS_principle))

Requirements
=============

**PHP >= 7.0**

Installation
=============

Package can be installed using composer `composer require cocoders/event-store`
Usage
=======
Library provides bunch of interfaces and about 5 concrete implementation for handling domain events.

1. Step 1 - define aggregate
In your aggregate you need implement `Cocoders\EventStore\AggregateRoot` insterface (you can use `Cocoders\EventStore\AggregateRootBehavior` trait if tou want).
    Example: [Example Domain Invoice Aggregate](tests/ExampleDomain/Invoice.php)

2. Step 2 - define own events and use it
You should define new event classes and use those events in you aggregate
    * [Example of using events in Invoice](tests/ExampleDomain/Invoice.php)
    * [Example of event class](tests/ExampleDomain/Invoice/Events/InvoiceAdded.php)

3. Step 3 - implement EventStore
You need to create concrete implementation of `Cocoders\EventStore\EventStore` interface, using your favorite db engine ;)
    Example: [Json File EventStore](tests/ExampleDomain/Infastracture/File/EventStore.php)

4. Step 4 - do some operation on aggregate and add fresh events from Aggregate to event store, and commit event store.

Examples:

```php
    $eventStore = new MyEventStore();
    $invoice = Invoice::issueInvoice(
        Invoice\Id::generate();
        $command->getSeller(),
        $command->getBuyer(),
        $command->maxItemNumber
    );
    $eventStore->apply($invoice->getRecordedEvents());
    $eventStore->commit();
```
    
* [Execute logic on aggregate](tests/ExampleDomain/UseCase/IssueInvoice.php)
* [Apply using repository pattern](tests/ExampleDomain/EventStore/Invoices.php)
* [Commit using command bus middleware](tests/ExampleDomain/CommandBus/EventStoreMiddleware.php)

5. Step 5 - define projections.
   As you can see in example, invoice aggregate does not have many "getters".
   You should generate read model using projection instead of using getters.
   Projection is basically event subscriber which can react to event by chaging read model.
   To use event subscriber you need to use EventBus:

```php
  $eventStore = new MyEventStore(); 
  $eventSubscribers = new EventSubscribers();
  $eventBus = new EventBus($eventSubscribers);
  $projectionManager = new ProjectionManager($eventSubscribers);
  $projectionManager->registerProjection(
      'InvoiceIssued',
      new MyProjection()
  );
  $newEventsStream = $eventStore->findUncommited();
  $eventStore->commit();
  $eventStore->notify($newEventsStream); //now event bus will notify projections as well
```
    
   * More examples:
        You can see whole working example setup in [IntegrationWithExampleDomainTest](tests/Tests/IntegrationWithExampleDomainTest.php)        


Development and tests
=================

To develop this lib we are using [docker](http://docker.io) and [docker-compose](https://docs.docker.com/compose/overview/).
After installation of those you should run:

```bash
    docker-compose up
    docker-compose run eventstore bash
```

Then on docker console run:

```bash
composer install
php bin/phpspec run -fpretty
php bin/phpunit
```


