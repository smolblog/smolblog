<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use DateTimeInterface;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Events\ContentCreated;
use Smolblog\Core\ContentV1\Markdown\NeedsMarkdownRendered;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a Reblog has been created.
 */
class ReblogCreated extends ContentCreated implements NeedsMarkdownRendered {
	use ReblogEventKit {
		getNewBody as internalGetBody;
		getNewTitle as internalGetTitle;
	}

	/**
	 * Construct the event.
	 *
	 * @param string                   $url              URL being reblogged.
	 * @param Identifier               $authorId         ID of the user that authored/owns this content.
	 * @param Identifier               $contentId        Identifier for the content this event is about.
	 * @param Identifier               $userId           User responsible for this event.
	 * @param Identifier               $siteId           Site this content belongs to.
	 * @param string|null              $comment          Additional comments on the URL.
	 * @param ExternalContentInfo|null $info             External info on the URL.
	 * @param DateTimeInterface|null   $publishTimestamp Date and time this content was first published.
	 * @param Identifier|null          $id               Optional identifier for this event.
	 * @param DateTimeInterface|null   $timestamp        Optional timestamp for this event.
	 */
	public function __construct(
		public readonly string $url,
		Identifier $authorId,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		public readonly ?string $comment = null,
		public readonly ?ExternalContentInfo $info = null,
		?DateTimeInterface $publishTimestamp = null,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		parent::__construct(
			publishTimestamp: $publishTimestamp,
			authorId: $authorId,
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
			id: $id,
			timestamp: $timestamp,
		);
	}

	/**
	 * Get the payload as an array.
	 *
	 * @return array
	 */
	public function getContentPayload(): array {
		return [
			'url' => $this->url,
			'comment' => $this->comment,
			'info' => $this->info->toArray(),
		];
	}

	/**
	 * Re-create this event from a payload
	 *
	 * @param array $payload Serialized event info.
	 * @return array
	 */
	protected static function contentPayloadFromArray(array $payload): array {
		return [
			...$payload,
			'info' => ExternalContentInfo::fromArray($payload['info']),
		];
	}

	/**
	 * Get the title of the created content.
	 *
	 * @return string
	 */
	public function getNewTitle(): string {
		return $this->internalGetTitle() ?? 'Reblog';
	}

	/**
	 * Get the body of the created content.
	 *
	 * @return string
	 */
	public function getNewBody(): string {
		return $this->internalGetBody() ?? '';
	}

	/**
	 * Get the class of the content this event creates.
	 *
	 * @return string
	 */
	public function getContentType(): string {
		return 'reblog';
	}
}
