<?php

namespace Smolblog\CoreDataFiles;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\EventListener;
use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use DateTimeZone;

/**
 * Store events for playback later.
 */
class EventStream implements EventListenerService {
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
