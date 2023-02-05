<?php

namespace Smolblog\Mock\EventStreams;

use DateTimeInterface;
use PDO;
use PDOStatement;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Framework\Messages\Attributes\EventStoreLayerListener;

/**
 * Persist ContentEvents
 */
class ContentEventStream {
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
			'INSERT INTO content_events (event_id, event_time, event_type, content_id, site_id, user_id, payload) ' .
			'VALUES (:id, :timestamp, :type, :contentId, :siteId, :userId, :payload)'
		);
	}

	/**
	 * Save the given ContentEvent to the stream.
	 *
	 * @param ContentEvent $event Event to save.
	 * @return void
	 */
	#[EventStoreLayerListener]
	public function onContentEvent(ContentEvent $event) {
		$this->addEvent->execute([
			'id' => $event->id->toByteString(),
			'timestamp' => $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
			'type' => get_class($event),
			'contentId' => $event->contentId->toByteString(),
			'siteId' => $event->siteId->toByteString(),
			'userId' => $event->userId->toByteString(),
			'payload' => json_encode($event->getPayload()),
		]);
	}
}
