<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\EventListener;
use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use DateTimeZone;
use Doctrine\DBAL\Schema\Schema;

/**
 * Store events for playback later.
 */
class EventStream implements EventListenerService, DatabaseTableHandler {
	/**
	 * Create the content table.
	 *
	 * @param Schema   $schema    Schema to add the content table to.
	 * @param callable $tableName Function to create a prefixed table name from a given table name.
	 * @return Schema
	 */
	public static function addTableToSchema(Schema $schema, callable $tableName): Schema {
		$table = $schema->createTable($tableName('event_stream'));
		$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$table->addColumn('event_uuid', 'guid');
		$table->addColumn('timestamp', 'datetimetz_immutable');
		$table->addColumn('user_uuid', 'guid');
		$table->addColumn('aggregate_uuid', 'guid', ['notnull' => false]);
		$table->addColumn('entity_uuid', 'guid', ['notnull' => false]);
		$table->addColumn('process_uuid', 'guid', ['notnull' => false]);
		$table->addColumn('event_obj', 'json');

		$table->setPrimaryKey(['dbid']);
		$table->addUniqueIndex(['event_uuid']);
		$table->addIndex(['aggregate_uuid']);
		$table->addIndex(['entity_uuid']);

		return $schema;
	}

	/**
	 * Create the service.
	 *
	 * @param DatabaseService      $db    Working database connection.
	 * @param SerializationService $serde Configured (de)serialization service.
	 */
	public function __construct(
		private DatabaseService $db,
		private SerializationService $serde,
	) {}

	/**
	 * Persist a DomainEvent.
	 *
	 * @param DomainEvent $event Event to persist.
	 * @return void
	 */
	#[EventListener]
	public function onDomainEvent(DomainEvent $event) {
		$this->db->insert('event_stream', [
			'event_uuid' => $event->id,
			'timestamp' => $event->timestamp->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.u'),
			'user_uuid' => $event->userId,
			'aggregate_uuid' => $event->aggregateId,
			'entity_uuid' => $event->entityId,
			'process_uuid' => $event->processId,
			'event_obj' => $this->serde->toJson($event),
		]);
	}
}
