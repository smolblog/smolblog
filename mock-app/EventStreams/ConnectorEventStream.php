<?php

namespace Smolblog\Mock\EventStreams;

use DateTimeInterface;
use PDO;
use PDOStatement;
use Smolblog\Core\Connector\Events\ConnectorEvent;
use Smolblog\Framework\Messages\Attributes\EventStoreLayerListener;

/**
 * Persist ConnectorEvents
 */
class ConnectorEventStream {
	/**
	 * Store the SQL statement we're using in case it helps.
	 *
	 * @var PDOStatement
	 */
	private PDOStatement $addEvent;

	/**
	 * Construct the service
	 *
	 * @param PDO $db Initialized DB connection.
	 */
	public function __construct(private PDO $db) {
		$this->addEvent = $this->db->prepare(
			'INSERT INTO connector_events (event_id, event_time, event_type, connection_id, user_id, payload) ' .
			'VALUES (:id, :timestamp, :type, :connectionId, :userId, :payload)'
		);
	}

	/**
	 * Save the given ConnectorEvent to the stream.
	 *
	 * @param ConnectorEvent $event Event to save.
	 * @return void
	 */
	#[EventStoreLayerListener]
	public function onConnectorEvent(ConnectorEvent $event) {
		$this->addEvent->execute([
			'id' => $event->id->toByteString(),
			'timestamp' => $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
			'type' => get_class($event),
			'connectionId' => $event->connectionId->toByteString(),
			'userId' => $event->userId->toByteString(),
			'payload' => json_encode($event->getPayload()),
		]);
	}
}
