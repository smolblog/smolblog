<?php

namespace Smolblog\Core\Content\Types\Picture;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentBodyEdited;
use Smolblog\Core\Content\Media\NeedsMediaObjects;
use Smolblog\Core\Content\Media\NeedsMediaRendered;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates that the media on this picture has changed.
 */
class PictureMediaEdited extends ContentBodyEdited implements NeedsMediaObjects, NeedsMediaRendered {
	/**
	 * Store the objects indicated by $mediaIds
	 *
	 * @var array
	 */
	private array $mediaObjects;

	/**
	 * Store the HTML that corresponds to the Media.
	 *
	 * @var array
	 */
	private array $mediaHtml;

	/**
	 * Store the rendered HTML for the caption if applicable.
	 *
	 * @var string|null
	 */
	private string $captionHtml = '';

	/**
	 * Store the title of the content for later use.
	 *
	 * @var string
	 */
	private string $title = '';

	/**
	 * Construct the event.
	 *
	 * @param Identifier[]           $mediaIds  Media to display.
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		public readonly array $mediaIds,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
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
	 * Get the event-specific fields.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return ['mediaIds' => $this->mediaIds];
	}

	/**
	 * Get the title for this content.
	 *
	 * @return string
	 */
	public function getNewTitle(): string {
		return $this->title;
	}

	/**
	 * Get the rendered HTML for this content.
	 *
	 * @return string
	 */
	public function getNewBody(): string {
		return join("\n\n", $this->mediaHtml) . "\n\n" . $this->captionHtml;
	}

	/**
	 * Set the computed title for the content at this point.
	 *
	 * @param string $title Computed title.
	 * @return void
	 */
	public function setTitle(string $title) {
		$this->title = $title;
	}

	/**
	 * Save the rendered markdown.
	 *
	 * @param string $html Rendered markdown.
	 * @return void
	 */
	public function setCaptionHtml(string $html): void {
		$this->captionHtml = $html;
	}

	/**
	 * Get the IDs of the attached Media.
	 *
	 * @return Identifier[]
	 */
	public function getMediaIds(): array {
		return $this->mediaIds;
	}

	/**
	 * Save the objects from the media IDs.
	 *
	 * @param array $objects Retrieved objects.
	 * @return void
	 */
	public function setMediaObjects(array $objects): void {
		$this->mediaObjects = $objects;
	}

	/**
	 * Get the Media objects to render.
	 *
	 * @return Media[]
	 */
	public function getMediaObjects(): array {
		return $this->mediaObjects;
	}

	/**
	 * Save the rendered Media HTML.
	 *
	 * @param string[] $html Rendered media objects.
	 * @return void
	 */
	public function setMediaHtml(array $html): void {
		$this->mediaHtml = $html;
	}

	/**
	 * Get the rendered HTML for the media.
	 *
	 * @return string[]
	 */
	public function getMediaHtml(): array {
		return $this->mediaHtml;
	}
}
