<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Fields\Markdown;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use Crell\Serde\Attributes\Field;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Channel\Events\ChannelSaved;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\ConnectionEstablished;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\CoreDataSql\Test\DataTestBase;

#[AllowMockObjectsWithoutExpectations]
final class EventStreamTest extends DataTestBase {
	public function testEventPersistence() {
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$this->app->container->get(SchemaRegistry::class); // Load the schema.
		$db = $env->getConnection();
		$this->assertEquals(0, $db->fetchOne('SELECT COUNT(*) FROM ' . $env->tableName('event_stream')));

		$userId = $this->randomId();
		$siteId = $this->randomId();
		$contentId = $this->randomId();
		$connectionId = Connection::buildId('test', 'onetwo');

		$expected = [
			new ConnectionEstablished(
				handler: 'test',
				handlerKey: 'onetwo',
				displayName: '@oneTwo',
				details: ['one' => 2],
				userId: $userId,
			),
			new ChannelSaved(
				new BasicChannel(
					handler: 'test',
					handlerKey: 'onetwo',
					displayName: '@oneTwo',
					userId: $userId,
					connectionId: $connectionId,
					details: ['eleven' => 22],
				),
				userId: $userId,
			),
			new ContentCreated(
				body: new Note(new Markdown('The night is full of holes')),
				aggregateId: $siteId,
				userId: $userId,
				entityId: $contentId,
			),
			new class (
				userId: $userId,
				aggregateId: $siteId,
				processId: $this->randomId(),
			) implements DomainEvent {
				use DomainEventKit;
				public function __construct(
					public readonly UuidInterface $userId,
					public readonly UuidInterface $aggregateId,
					public readonly UuidInterface $processId,
				) {
					$this->setIdAndTime(null, null);
				}
				#[Field(exclude: true)]
				public null $entityId { get => null; }
			},
		];

		foreach ($expected as $event) {
			$this->app->dispatch($event);
		}

		$serde = $this->app->container->get(SerializationService::class);

		$this->assertEquals(
			array_map(fn($evt) => $serde->toJson($evt), $expected),
			$db->fetchFirstColumn('SELECT event_obj FROM ' . $env->tableName('event_stream')),
		);
	}
}
