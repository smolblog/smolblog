<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use DateTimeInterface;
use Smolblog\Core\ContentV1\Events\ContentBodyEdited;
use Smolblog\Core\ContentV1\Markdown\NeedsMarkdownRendered;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates that the media on this picture has changed.
 */
class PictureCaptionEdited extends ContentBodyEdited implements NeedsMarkdownRendered {
	/**
	 * Store the HTML for the media at this point.
	 *
	 * @var string
	 */
	private string $mediaHtml;

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
	 * @param string                 $caption   Caption for the picture.
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		public readonly string $caption,
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
		return ['caption' => $this->caption];
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
		return $this->mediaHtml . "\n\n" . $this->captionHtml;
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
	 * Get the markdown to render.
	 *
	 * @return string[]
	 */
	public function getMarkdown(): array {
		return [$this->caption];
	}

	/**
	 * Save the rendered markdown.
	 *
	 * @param string[] $html Rendered markdown.
	 * @return void
	 */
	public function setMarkdownHtml(array $html): void {
		$this->captionHtml = $html[0] ?? '';
	}

	/**
	 * Get the rendered HTML for the caption.
	 *
	 * @return string
	 */
	public function getCaptionHtml(): string {
		return $this->captionHtml;
	}

	/**
	 * Save the rendered Media HTML.
	 *
	 * @param string $html Rendered media objects.
	 * @return void
	 */
	public function setAllMediaHtml(string $html): void {
		$this->mediaHtml = $html;
	}
}
