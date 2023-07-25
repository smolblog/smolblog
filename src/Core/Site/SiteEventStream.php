<?php

namespace Smolblog\Core\Site;

use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use Smolblog\Framework\Messages\Attributes\EventStoreLayerListener;
use Smolblog\Framework\Messages\Listener;

/**
 * Persist the content events.
 *
 * Expects a table like:
 *
 * event_uuid varchar(40) NOT NULL UNIQUE,
 * event_time varchar(30) NOT NULL,
 * site_uuid varchar(40) NOT NULL,
 * user_uuid varchar(40) NOT NULL,
 * event_type varchar(50) NOT NULL,
 * payload text,
 */
class SiteEventStream implements Listener {
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
	 * Save the given SiteEvent to the stream.
	 *
	 * @param SiteEvent $event Event to save.
	 * @return void
	 */
	#[EventStoreLayerListener()]
	public function onSiteEvent(SiteEvent $event) {
		$data = [
			'event_uuid' => $event->id->toString(),
			'event_time' => $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
			'site_uuid' => $event->siteId->toString(),
			'user_uuid' => $event->userId->toString(),
			'event_type' => get_class($event),
			'payload' => json_encode($event->getPayload()),
		];

		$this->db->table('site_events')->insert($data);
	}
}
