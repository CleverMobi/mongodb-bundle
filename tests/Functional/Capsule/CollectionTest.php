<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Capsule;

use Prophecy\PhpUnit\ProphecyTrait;
use Facile\MongoDbBundle\Services\ClientRegistry;
use MongoDB\Client;
use Facile\MongoDbBundle\Capsule\Collection;
use Facile\MongoDbBundle\Event\QueryEvent;
use Facile\MongoDbBundle\Tests\Functional\AppTestCase;
use MongoDB\Driver\Manager;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CollectionTest extends AppTestCase
{
    use ProphecyTrait;

    private function getManager(): Manager
    {
        /** @var ClientRegistry $reg */
        $reg = $this->getContainer()->get('mongo.client_registry');
        /** @var Client $client */
        $client = $reg->getClient('test_client', 'testdb');

        return $client->__debugInfo()['manager'];
    }

    public function test_construction(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        self::assertInstanceOf(\MongoDB\Collection::class, $coll);
    }

    public function test_insertOne(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->insertOne(['test' => 1]);
    }

    public function test_updateOne(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->updateOne(['filter' => 1], ['$set' => ['testField' => 1]]);
    }

    public function test_count(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->count(['test' => 1]);
    }

    public function test_countDocuments(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->countDocuments(['test' => 1]);
    }

    public function test_find(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->find([]);
    }

    public function test_findOne(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->findOne([]);
    }

    public function test_findOneAndUpdate(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->findOneAndUpdate([], ['$set' => ['country' => 'us']]);
    }

    public function test_findOneAndDelete(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->findOneAndDelete([]);
    }

    public function test_deleteOne(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->deleteOne([]);
    }

    public function test_replaceOne(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->replaceOne([], []);
    }

    public function test_aggregate(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->deleteMany([]);

        $coll->insertOne(['group' => 'a', 'testValue' => 2]);
        $coll->insertOne(['group' => 'a', 'testValue' => 3]);
        $coll->insertOne(['group' => 'b', 'testValue' => 2]);

        $result = $coll->aggregate([
            ['$match' => ['group' => 'a']],
            ['$group' => ['_id' => '$group', 'value' => ['$sum' => '$testValue']]],
        ]);

        $results = [];
        foreach ($result as $res) {
            $res = (array) $res;
            $results[] = ['group' => $res['_id'], 'value' => $res['value']];
        }

        self::assertCount(1, $results);
        self::assertEquals('a', $results[0]['group']);
        self::assertEquals(5, $results[0]['value']);
    }

    /** leave this test as last one to clean the collection*/
    public function test_deleteMany(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->deleteMany([]);
    }

    public function test_distinct(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_collection', [], $ev->reveal());

        $coll->distinct('field');
    }

    public function test_estimatedDocumentCount(): void
    {
        $manager = $this->getManager();
        $ev = $this->prophesize(EventDispatcherInterface::class);
        $this->assertEventsDispatching($ev);

        $coll = new Collection($manager, 'test_client', 'testdb', 'test_document', [], $ev->reveal());

        $coll->estimatedDocumentCount();
    }

    protected function assertEventsDispatching($ev)
    {
        $ev->dispatch(Argument::type(QueryEvent::class), QueryEvent::QUERY_PREPARED)->shouldBeCalled();
        $ev->dispatch(Argument::type(QueryEvent::class), QueryEvent::QUERY_EXECUTED)->shouldBeCalled();
    }
}
