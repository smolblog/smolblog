<?php

namespace Smolblog\Core\Connector\Data;

use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Connector\Events\ConnectorEvent;
use Smolblog\Foundation\Service\Messaging\PersistEventListener;
use Smolblog\Foundation\Service\Messaging\Listener;

/**
 * Persist the Connector Events.
 *
 * Expects a table like:
 * event_uuid varchar(40) NOT NULL UNIQUE,
 * event_time varchar(30) NOT NULL,
 * connection_uuid varchar(40) NOT NULL,
 * user_uuid varchar(40) NOT NULL,
 * event_type varchar(255) NOT NULL,
 * payload text,
 */
class ConnectorEventStream implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param ConnectionInterface $db Working DB connection.
	 */
	public function __construct(
		private ConnectionInterface $db,
	) {
	}

	/**
	 * Save the given ConnectorEvent to the stream.
	 *
	 * @param ConnectorEvent $event Event to save.
	 * @return void
	 */
	#[PersistEventListener()]
	public function onConnectorEvent(ConnectorEvent $event) {
		$data = [
			'event_uuid' => $event->id->toString(),
			'event_time' => $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
			'connection_uuid' => $event->connectionId->toString(),
			'user_uuid' => $event->userId->toString(),
			'event_type' => get_class($event),
			'payload' => json_encode($event->getPayload()),
		];

		$this->db->table('connector_events')->insert($data);
	}
}
