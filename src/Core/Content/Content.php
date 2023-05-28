<?php

namespace Smolblog\Core\Content;

use DateTimeInterface;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\Entity;
use Smolblog\Framework\Objects\Identifier;

/**
 * Base class for content.
 *
 * This is the foundation for all pieces of content in the system. There are two hooks available:
 *
 * 1) Content types can store and handle their data as they see fit. The system can interact with that data
 *    through the getTitle and getHtmlContent functions.
 * 2) Content extensions can attach extra data to the content through the attachExtension function.
 *
 * Remember, the canonical store for all data is the event stream! The Content class is intented to provide a
 * view into the data, but there may be other data accessable in other ways.
 */
class Content extends Entity {
	/**
	 * Handles the body and title.
	 *
	 * @var ContentType
	 */
	public readonly ContentType $type;

	/**
	 * Relative URL for this content.
	 *
	 * @var string
	 */
	public readonly ?string $permalink;

	/**
	 * Date and time this content was first published.
	 *
	 * @var DateTimeInterface
	 */
	public readonly ?DateTimeInterface $publishTimestamp;

	/**
	 * Visiblity of the content.
	 *
	 * @var ContentVisibility
	 */
	public readonly ContentVisibility $visibility;

	/**
	 * ID of the site this content belongs to.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $siteId;

	/**
	 * ID of the user that authored/owns this content.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $authorId;

	/**
	 * Extensions attached to this content.
	 *
	 * @var ContentExtension[]
	 */
	public readonly array $extensions;

	/**
	 * Construct the content
	 *
	 * @throws InvalidContentException Thrown if an invalid state is given.
	 *
	 * @param ContentType        $type             ContentType that handles body and title.
	 * @param Identifier         $siteId           ID of the site this content belongs to.
	 * @param Identifier         $authorId         ID of the user that authored/owns this content.
	 * @param string             $permalink        Relative URL for this content.
	 * @param DateTimeInterface  $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility  $visibility       Visiblity of the content.
	 * @param Identifier|null    $id               ID of this content.
	 * @param ContentExtension[] $extensions       Extensions attached to this content.
	 */
	public function __construct(
		ContentType $type,
		Identifier $siteId,
		Identifier $authorId,
		?string $permalink = null,
		?DateTimeInterface $publishTimestamp = null,
		ContentVisibility $visibility = ContentVisibility::Draft,
		?Identifier $id = null,
		array $extensions = [],
	) {
		// TODO: Check for $permalink when published. Currently ignoring since WordPress doesn't assign permalinks until
		// after publishing.
		if ($visibility === ContentVisibility::Published && (!isset($publishTimestamp))) {
			throw new InvalidContentException('Permalink and timestamp are required if content is published.');
		}

		$this->type = $type;
		$this->permalink = $permalink;
		$this->publishTimestamp = $publishTimestamp;
		$this->visibility = $visibility;
		$this->siteId = $siteId;
		$this->authorId = $authorId;
		$this->extensions = $extensions;
		parent::__construct(id: $id ?? new DateIdentifier());
	}

	/**
	 * Serialize this array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return [
			'id' => $this->id->toString(),
			'siteId' => $this->siteId->toString(),
			'authorId' => $this->authorId->toString(),
			'permalink' => $this->permalink,
			'publishTimestamp' => $this->publishTimestamp?->format(DateTimeInterface::RFC3339_EXTENDED),
			'visibility' => $this->visibility->value,
			'title' => $this->type->getTitle(),
			'body' => $this->type->getBodyContent(),
			'contentType' => [
				...$this->type->toArray(),
				'type' => get_class($this->type),
			],
			'extensions' => array_map(fn($ext) => $ext->toArray(), $this->extensions),
		];
	}

	/**
	 * Create content from a serialized array
	 *
	 * @param array $data Serialized data.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		$type = null;
		if (isset($data['contentType'])) {
			$contentTypeClass = $data['contentType']['type'];
			unset($data['contentType']['type']);

			$type = $contentTypeClass::fromArray($data['contentType']);
		} else {
			$type = new GenericContent(title: $data['title'], body: $data['body']);
		}

		unset($data['contentType']);
		unset($data['title']);
		unset($data['body']);

		$extensions = [];
		foreach ($data['extensions'] as $extClass => $extData) {
			$extensions[$extClass] = $extClass::fromArray($extData);
		}

		return new Content(
			id: Identifier::fromString($data['id']),
			type: $type,
			siteId: Identifier::fromString($data['siteId']),
			authorId: Identifier::fromString($data['authorId']),
			permalink: $data['permalink'] ?? null,
			publishTimestamp: self::safeDeserializeDate($data['publishTimestamp']),
			visibility: ContentVisibility::tryFrom($data['visibility']),
			extensions: $extensions,
		);
	}
}
