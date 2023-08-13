<?php

namespace Smolblog\Core\Content\Types\Picture;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Markdown\NeedsMarkdownRendered;
use Smolblog\Core\Content\Media\NeedsMediaObjects;
use Smolblog\Core\Content\Media\NeedsMediaRendered;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a Picture has been created.
 */
class PictureCreated extends ContentCreated implements NeedsMarkdownRendered, NeedsMediaObjects, NeedsMediaRendered {
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
	 * Construct the event.
	 *
	 * @param Identifier[]           $mediaIds         Media to display.
	 * @param string|null            $caption          Caption for the picture.
	 * @param Identifier             $authorId         ID of the user that authored/owns this content.
	 * @param Identifier             $contentId        Identifier for the content this event is about.
	 * @param Identifier             $userId           User responsible for this event.
	 * @param Identifier             $siteId           Site this content belongs to.
	 * @param DateTimeInterface      $publishTimestamp Date and time this content was first published.
	 * @param Identifier|null        $id               Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp        Optional timestamp for this event.
	 */
	public function __construct(
		public readonly array $mediaIds,
		public readonly ?string $caption = null,
		Identifier $authorId,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?DateTimeInterface $publishTimestamp = null,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(
			authorId: $authorId,
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
			publishTimestamp: $publishTimestamp,
			id: $id,
			timestamp: $timestamp,
		);
	}

	/**
	 * Get the content type ('picture').
	 *
	 * @return string
	 */
	public function getContentType(): string {
		return 'picture';
	}

	/**
	 * Get the title for this content.
	 *
	 * @return string
	 */
	public function getNewTitle(): string {
		return isset($this->caption) ? Note::truncateText($this->caption) : $this->mediaObjects[0]->title;
	}

	/**
	 * Get the rendered HTML for this content.
	 *
	 * @return string
	 */
	public function getNewBody(): string {
		$mediaHtmlBlocks = $this->mediaHtml ?? Picture::createBasicHtmlForImageMedia($this->mediaObjects);

		return join("\n\n", $mediaHtmlBlocks) . "\n\n" . $this->captionHtml;
	}

	/**
	 * Get properties specific to this event.
	 *
	 * @return array
	 */
	protected function getContentPayload(): array {
		return [
			'mediaIds' => array_map(fn($id) => $id->toString(), $this->mediaIds),
			'caption' => $this->caption,
		];
	}

	/**
	 * Get the markdown to render.
	 *
	 * @return string[]
	 */
	public function getMarkdown(): array {
		return array_filter([$this->caption]);
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
}
