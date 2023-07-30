<?php

namespace Smolblog\Core\Content\Media;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicate that a new piece of media has been uploaded and processed.
 */
class MediaAdded extends ContentEvent {
	/**
	 * Construct the event.
	 *
	 * @param Identifier             $contentId         ID of the Media object.
	 * @param Identifier             $userId            User uploading the media.
	 * @param Identifier             $siteId            Site media is being uploaded to.
	 * @param string                 $title             Title of the media.
	 * @param string                 $accessibilityText Text-only description of the media.
	 * @param MediaType              $type              Broad type of the media.
	 * @param string                 $thumbnailUrl      URL for a thumbnail image.
	 * @param string                 $defaultUrl        URL for a default version of the media.
	 * @param string                 $defaultHtml       Default HTML for the media.
	 * @param MediaFile              $file              Information on the corresponding file.
	 * @param Identifier|null        $id                ID of the event.
	 * @param DateTimeInterface|null $timestamp         Timestamp of the event.
	 */
	public function __construct(
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		public readonly string $title,
		public readonly string $accessibilityText,
		public readonly MediaType $type,
		public readonly string $thumbnailUrl,
		public readonly string $defaultUrl,
		public readonly string $defaultHtml,
		public readonly MediaFile $file,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
			id: $id,
			timestamp: $timestamp,
		);
	}

	/**
	 * Get the payload for this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'title' => $this->title,
			'accessibilityText' => $this->accessibilityText,
			'type' => $this->type->value,
			'thumbnailUrl' => $this->thumbnailUrl,
			'defaultUrl' => $this->defaultUrl,
			'defaultHtml' => $this->defaultHtml,
			'file' => $this->file->toArray(),
		];
	}

	/**
	 * Deserialize a payload for this event.
	 *
	 * @param array $payload Serialized payload.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		$payload['type'] = MediaType::tryFrom($payload['type'] ?? '');
		$payload['file'] = MediaFile::fromArray($payload['file']);
		return $payload;
	}
}
