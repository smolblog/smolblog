<?php

namespace Smolblog\Core\Content\Events;

use DateTimeInterface;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentNotProjectedException;
use Smolblog\Framework\Messages\Event;
use Smolblog\Framework\Messages\PayloadKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Base event for Content-related events.
 *
 * All Content-related events need an ID for the piece of content and a user initiating the event. Everything else is
 * up to the subclass. For the sake of compatability, implementing projections should also attach the current state
 * of the content as both its native object and the standard Content object when the event is projected. This will
 * allow listeners further down the chain to interact with either the the standard object or the native content at
 * the appropriate point in its lifecycle.
 */
abstract class ContentEvent extends Event {
	use PayloadKit;

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
	 * The state of the content as of this event in its native format
	 *
	 * @var mixed
	 */
	protected mixed $nativeContent = null;

	/**
	 * The state of the content as of this event in the standard format
	 *
	 * @var mixed
	 */
	protected Content $standardContent = null;

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
		parent::__construct(id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the state of the content as of this event in its native format.
	 *
	 * @throws ContentNotProjectedException Thrown if the content is not yet projected.
	 *
	 * @return mixed
	 */
	public function getNativeContent(): mixed {
		if (!isset($this->nativeContent)) {
			throw new ContentNotProjectedException(event: $this);
		}

		return $this->nativeContent;
	}

	/**
	 * Get the state of the content as of this event in the standard format.
	 *
	 * @throws ContentNotProjectedException Thrown if the content is not yet projected.
	 *
	 * @return mixed
	 */
	public function getStandardContent(): Content {
		if (!isset($this->standardContent)) {
			throw new ContentNotProjectedException(event: $this);
		}

		return $this->standardContent;
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
		];
	}
}
