<?php

namespace Smolblog\Core\Connector\Data;

use Closure;
use DateTimeImmutable;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\Connector\Events\ConnectorEvent;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\RandomIdentifier;
use Smolblog\Test\TestCase;
use stdClass;

final class ConnectorEventStreamTest extends TestCase {
	private Connection $db;

	private function tempDatabaseWithTable(string $name, Closure $builder): Connection {
		$manager = new Manager();
		$manager->addConnection([
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		]);
		$manager->getConnection()->getSchemaBuilder()->create($name, $builder);

		return $manager->getConnection();
	}

	private ConnectorEventStream $stream;

	protected function setUp(): void {
		$this->db = $this->tempDatabaseWithTable('connector_events', function(Blueprint $table) {
			$table->uuid('event_uuid')->primary();
			$table->dateTimeTz('event_time');
			$table->uuid('connection_uuid');
			$table->uuid('user_uuid');
			$table->string('event_type');
			$table->text('payload');
		});

		$this->stream = new ConnectorEventStream(db: $this->db);
	}

	public function testItPersistsAConnectorEvent() {
		$event = new class() extends ConnectorEvent {
			public function __construct() {
				parent::__construct(
					connectionId: Identifier::fromString('3bf85790-60f7-41f5-a75f-a3b806be6a58'),
					userId: Identifier::fromString('dfd72da8-827e-472c-9a58-9d1ce3ed4482'),
					id: Identifier::fromString('8289a96d-e8c7-4c6a-8d6e-143436c59ec2'),
					timestamp: new DateTimeImmutable('2022-02-22 02:02:02+00:00'),
				);
			}

			public function getPayload(): array {
				return ['one' => 'two', 'three' => 'four'];
			}
		};

		$expected = new stdClass();
		$expected->event_uuid = '8289a96d-e8c7-4c6a-8d6e-143436c59ec2';
		$expected->event_time = '2022-02-22T02:02:02.000+00:00';
		$expected->connection_uuid = '3bf85790-60f7-41f5-a75f-a3b806be6a58';
		$expected->user_uuid = 'dfd72da8-827e-472c-9a58-9d1ce3ed4482';
		$expected->event_type = get_class($event);
		$expected->payload = '{"one":"two","three":"four"}';

		$this->stream->onConnectorEvent($event);

		$this->assertEquals($expected, $this->db->table('connector_events')->first());
	}
}
