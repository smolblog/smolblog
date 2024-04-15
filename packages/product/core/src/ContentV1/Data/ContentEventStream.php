<?php

namespace Smolblog\Core\ContentV1\Data;

use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Foundation\Service\Messaging\PersistEventListener;
use Smolblog\Foundation\Service\Messaging\Listener;

/**
 * Persist the content events.
 *
 * Expects a table like:
 *
 * event_uuid varchar(40) NOT NULL UNIQUE,
 * event_time varchar(30) NOT NULL,
 * content_uuid varchar(40) NOT NULL,
 * site_uuid varchar(40) NOT NULL,
 * user_uuid varchar(40) NOT NULL,
 * event_type varchar(50) NOT NULL,
 * payload text,
 */
class ContentEventStream implements Listener {
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
	 * Save the given ContentEvent to the stream.
	 *
	 * @param ContentEvent $event Event to save.
	 * @return void
	 */
	#[PersistEventListener()]
	public function onContentEvent(ContentEvent $event) {
		$data = [
			'event_uuid' => $event->id->toString(),
			'event_time' => $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
			'content_uuid' => $event->contentId->toString(),
			'site_uuid' => $event->siteId->toString(),
			'user_uuid' => $event->userId->toString(),
			'event_type' => get_class($event),
			'payload' => json_encode($event->getPayload()),
		];

		$this->db->table('content_events')->insert($data);
	}
}
