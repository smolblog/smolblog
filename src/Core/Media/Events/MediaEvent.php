<?php

namespace Smolblog\Core\Media\Events;

use DateTimeInterface;
use Smolblog\Framework\Messages\Event;
use Smolblog\Framework\Messages\PayloadKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Base class for Media events.
 */
abstract class MediaEvent extends Event {
	use PayloadKit;

	/**
	 * Identifier for the media this event is about.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $mediaId;

	/**
	 * User responsible for this event.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $userId;

	/**
	 * Identifier for the site this media belongs to.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $siteId;

	/**
	 * Construct the event
	 *
	 * @param Identifier             $mediaId   Identifier for the media this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this media belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		Identifier $mediaId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		$this->mediaId = $mediaId;
		$this->userId = $userId;
		$this->siteId = $siteId;
		parent::__construct(id: $id, timestamp: $timestamp);
	}

	/**
	 * Deserialize the properties unique to this event.
	 *
	 * @param array $properties Properties to deserialize.
	 * @return array
	 */
	protected static function standardPropertiesFromArray(array $properties): array {
		return array_map(self::class . '::safeDeserializeIdentifier', $properties);
	}

	/**
	 * Get the main properties for this Event.
	 *
	 * @return array
	 */
	private function getStandardProperties(): array {
		return [
			'mediaId' => strval($this->mediaId),
			'userId' => strval($this->userId),
			'siteId' => strval($this->siteId),
		];
	}
}
