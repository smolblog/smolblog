<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Core\ContentV1\Content;
use Smolblog\Core\ContentV1\ContentBuilder;
use Smolblog\Core\ContentV1\ContentBuilderKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Base class for singluar content queries.
 *
 * Sets up memoization, content extensions, and a visibility security check.
 *
 * Use GenericContentById for a concrete query.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
abstract class BaseContentById extends MemoizableQuery implements ContentBuilder, AuthorizableMessage {
	use ContentBuilderKit;

	/**
	 * True if the content with given ID does not exist.
	 *
	 * @var boolean
	 */
	protected bool $notFound = false;

	/**
	 * Construct the query.
	 *
	 * @param Identifier      $siteId    ID of the site to pull from.
	 * @param Identifier      $contentId ID of the content being queried.
	 * @param Identifier|null $userId    Optional user making the request.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $contentId,
		public readonly ?Identifier $userId = null
	) {
	}

	/**
	 * Get the ID of the content in question.
	 *
	 * @return Identifier
	 */
	public function getContentId(): Identifier {
		return $this->contentId;
	}

	/**
	 * Directly set the Content.
	 *
	 * If `null` is given, the content is assumed "not found" and the query is halted.
	 *
	 * @param mixed $results Fully-formed Content or null if not found.
	 * @return void
	 */
	public function setResults(mixed $results): void {
		if (!isset($results)) {
			$this->notFound = true;
			$this->stopMessage();
		}

		$this->results = $results;
	}

	/**
	 * Get the content from the ContentBuilder.
	 *
	 * @return Content|null
	 */
	public function results(): ?Content {
		if ($this->notFound) {
			return null;
		}

		return $this->results ?? $this->getContent();
	}

	/**
	 * Get the authorization query and check if the given user can see the given content.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new ContentVisibleToUser(siteId: $this->siteId, contentId: $this->contentId, userId: $this->userId);
	}
}
