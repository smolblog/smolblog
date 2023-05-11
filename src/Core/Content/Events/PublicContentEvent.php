<?php

namespace Smolblog\Core\Content\Events;

use DateTimeInterface;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentExtension;
use Smolblog\Core\Content\ContentType;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Identifier;

/**
 * For events that indicate something has changed to the public-facing content.
 *
 * Changes to public-facing content are significant events that need their own callout. They trigger changes not only
 * to the public-facing website and feeds but also require push notifications, sending content to other channels,
 * and/or alerting ActivityPub subscribers.
 *
 * It is recommended to extend and listen to the child events, `PublicContentAdded`, `PublicContentChanged`, and
 * `PublicContentRemoved`.
 */
abstract class PublicContentEvent extends ContentEvent {
	/**
	 * Store the in-progress properties of the content.
	 *
	 * An associative array that will hold the different aspects of the Content until it is ready to be fully
	 * instantiated.
	 *
	 * @var array
	 */
	protected array $contentProps = [];

	/**
	 * The full Content object as of this event.
	 *
	 * @var Content
	 */
	protected Content $contentState;

	/**
	 * Get the state of the content as of this event.
	 *
	 * If the required data has not been given, this will throw an error or exception.
	 *
	 * @return Content
	 */
	public function getContent(): Content {
		$this->contentState ??= new Content(...$this->contentProps);
		return $this->contentState;
	}

	/**
	 * Set the type of the Content
	 *
	 * @param ContentType $type ContentType for this Content.
	 * @return void
	 */
	public function setContentType(ContentType $type): void {
		$this->contentProps['type'] = $type;
	}

	/**
	 * Set an Extension on this Content.
	 *
	 * @param ContentExtension $extension Extension to add to this Content.
	 * @return void
	 */
	public function setContentExtension(ContentExtension $extension): void {
		$this->contentProps['extensions'] ??= [];
		$this->contentProps['extensions'][get_class($extension)] = $extension;
	}

	/**
	 * Set one or more base properties on the Content.
	 *
	 * @param Identifier|null        $id               ID of this content.
	 * @param Identifier|null        $siteId           ID of the site this content belongs to.
	 * @param Identifier|null        $authorId         ID of the user that authored/owns this content.
	 * @param string|null            $permalink        Relative URL for this content.
	 * @param DateTimeInterface|null $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility|null $visibility       Visiblity of the content.
	 * @return void
	 */
	public function setContentProperty(
		?Identifier $id = null,
		?Identifier $siteId = null,
		?Identifier $authorId = null,
		?string $permalink = null,
		?DateTimeInterface $publishTimestamp = null,
		?ContentVisibility $visibility = null,
	): void {
		array_merge($this->contentProps, array_filter([
			'id' => $id,
			'siteId' => $siteId,
			'authorId' => $authorId,
			'permalink' => $permalink,
			'publishTimestamp' => $publishTimestamp,
			'visibility' => $visibility,
		], fn($val) => isset($val)));
	}

	/**
	 * Empty payload (for now).
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [];
	}
}
