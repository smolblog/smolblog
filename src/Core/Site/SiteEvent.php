<?php

namespace Smolblog\Core\Site;

use DateTimeInterface;
use Smolblog\Framework\Messages\Event;
use Smolblog\Framework\Messages\PayloadKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Event dealing with general site-level actions.
 */
abstract class SiteEvent extends Event {
	use PayloadKit;

	/**
	 * Construct the event.
	 *
	 * If an action is being taken by the system and not a user, use Smolblog\Core\User\User::internalSystemUser().
	 *
	 * @param Identifier             $siteId    Site this event belongs to.
	 * @param Identifier             $userId    User instigating the event.
	 * @param Identifier|null        $id        ID of the event.
	 * @param DateTimeInterface|null $timestamp Time of the event.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		parent::__construct(id: $id, timestamp: $timestamp);
	}

	/**
	 * Deserialize the standarnd properties
	 *
	 * @param array $properties Associative array of standard properties.
	 * @return array
	 */
	protected static function standardPropertiesFromArray(array $properties): array {
		return array_map(fn($item) => Identifier::fromString($item), $properties);
	}

	/**
	 * Get the properties defined on this class as a serialized array.
	 *
	 * @return array
	 */
	private function getStandardProperties(): array {
		return [
			'siteId' => $this->siteId->toString(),
			'userId' => $this->userId->toString(),
		];
	}
}
