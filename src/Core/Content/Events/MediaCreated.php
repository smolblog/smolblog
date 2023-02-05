<?php

namespace Smolblog\Core\Content\Events;

use DateTimeInterface;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a piece of media has been added to the media library.
 */
class MediaCreated extends ContentEvent {
	/**
	 * Final URL of the media file referenced.
	 *
	 * @var string
	 */
	public readonly string $url;

	/**
	 * Text description of the media.
	 *
	 * @var string
	 */
	public readonly string $descriptiveText;

	/**
	 * Arbitrary attributes for the media (dimensions, autoplay, etc.).
	 *
	 * @var array
	 */
	public readonly array $attributes;

	/**
	 * Create the event
	 *
	 * @param Identifier             $contentId       Identifier for the media this event is about.
	 * @param Identifier             $userId          User responsible for this event.
	 * @param Identifier             $siteId          Site this media belongs to.
	 * @param string                 $url             Final URL of the media file referenced.
	 * @param string                 $descriptiveText Text description of the media.
	 * @param array                  $attributes      Arbitrary attributes for the media (dimensions, autoplay, etc.).
	 * @param Identifier|null        $id              Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp       Optional timestamp for this event.
	 */
	public function __construct(
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		string $url,
		string $descriptiveText,
		array $attributes,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		$this->url = $url;
		$this->descriptiveText = $descriptiveText;
		$this->attributes = $attributes;
		parent::__construct(
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
			id: $id,
			timestamp: $timestamp
		);
	}

	/**
	 * Get the fields unique to this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'url' => $this->url,
			'descriptiveText' => $this->descriptiveText,
			'attributes' => $this->attributes,
		];
	}
}
