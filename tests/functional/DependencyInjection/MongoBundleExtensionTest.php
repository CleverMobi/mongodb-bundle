<?php

namespace MongoBundle\Tests\functional\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use MongoBundle\DependencyInjection\MongoBundleExtension;
use MongoDB\Client;
use MongoDB\Database;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class MongoBundleExtensionTest extends AbstractExtensionTestCase
{
    public function test_load()
    {
        $this->load(
            [
                'host' => 'localhost',
                'database' => 'telegraf',
                'username' => 'test',
                'password' => 'password',
            ]
        );
        $this->compile();
        // Alias connections
        $this->assertContainerBuilderHasService('mongo.connection', Database::class);
        $defaultConnection = $this->container->get('mongo.connection');
        $this->assertInstanceOf(Database::class, $defaultConnection);
        $this->assertSame('telegraf', $defaultConnection->getDatabaseName());
        // 'default' connections
        $this->assertContainerBuilderHasService('mongo.connection.default', Database::class);
        $defaultConnection = $this->container->get('mongo.connection.default');

        $this->assertInstanceOf(Database::class, $defaultConnection);
        $this->assertSame('telegraf', $defaultConnection->getDatabaseName());
    }

    public function test_load_multiple()
    {
        $this->load(
            [
                'default_connection' => 'default',
                'connections' => [
                    'default' => [
                        'host' => 'localhost',
                        'database' => 'telegraf',
                        'username' => 'test',
                        'password' => 'password',
                    ],
                    'test' => [
                        'host' => 'localhost',
                        'database' => 'tele',
                        'username' => 'test',
                        'password' => 'password',
                    ],
                ],

            ]
        );
        $this->compile();
        // Alias connections
        $this->assertContainerBuilderHasService('mongo.connection', Database::class);
        $defaultConnection = $this->container->get('mongo.connection');
        $this->assertInstanceOf(Database::class, $defaultConnection);
        $this->assertSame('telegraf', $defaultConnection->getDatabaseName());
        // 'default' connections
        $this->assertContainerBuilderHasService('mongo.connection.default', Database::class);
        $defaultConnection = $this->container->get('mongo.connection.default');
        $this->assertInstanceOf(Database::class, $defaultConnection);
        $this->assertSame('telegraf', $defaultConnection->getDatabaseName());
        // 'test' connections
        $this->assertContainerBuilderHasService('mongo.connection.test', Database::class);
        $testConnection = $this->container->get('mongo.connection.test');
        $this->assertInstanceOf(Database::class, $testConnection);
        $this->assertSame('tele', $testConnection->getDatabaseName());
    }

    /**
     * Return an array of container extensions you need to be registered for each test (usually just the container
     * extension you are testing.
     *
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [
            new MongoBundleExtension(),
        ];
    }
}
