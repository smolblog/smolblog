<?php

namespace Smolblog\Core\ContentV1\Events;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Base event for Content-related events.
 *
 * All Content-related events need an ID for the piece of content and a user initiating the event. Everything else is
 * up to the subclass. For the sake of compatability, implementing projections should also attach the current state
 * of the content as both its native object and the standard Content object when the event is projected. This will
 * allow listeners further down the chain to interact with either the the standard object or the native content at
 * the appropriate point in its lifecycle.
 */
abstract readonly class ContentEvent extends DomainEvent {
	/**
	 * Identifier for the content this event is about.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $contentId;

	/**
	 * User responsible for this event.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $userId;

	/**
	 * Identifier for the site this content belongs to.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $siteId;

	/**
	 * Construct the event
	 *
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		$this->contentId = $contentId;
		$this->userId = $userId;
		$this->siteId = $siteId;
		parent::__construct(
			id: $id,
			timestamp: new DateTimeField($timestamp?->format(DateTimeInterface::RFC3339_EXTENDED) ?? 'now'),
			userId: $userId,
			aggregateId: $siteId,
			entityId: $contentId,
		);
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
			'contentId' => $this->contentId->toString(),
			'userId' => $this->userId->toString(),
			'siteId' => $this->siteId->toString(),
		];
	}
}
