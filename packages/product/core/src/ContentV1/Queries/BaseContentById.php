<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Core\ContentV1\Content;
use Smolblog\Core\ContentV1\ContentBuilder;
use Smolblog\Core\ContentV1\ContentBuilderKit;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Service\Messaging\MemoizableQuery;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\Memoizable;
use Smolblog\Foundation\Value\Traits\MemoizableKit;

/**
 * Base class for singluar content queries.
 *
 * Sets up memoization, content extensions, and a visibility security check.
 *
 * Use GenericContentById for a concrete query.
 */
abstract readonly class BaseContentById extends Query implements Memoizable, ContentBuilder, AuthorizableMessage {
	use MemoizableKit;
	use ContentBuilderKit;

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
			$this->setMetaValue('notFound', true);
			$this->stopMessage();
		}

		$this->setResults($results);
	}

	/**
	 * Get the content from the ContentBuilder.
	 *
	 * @return Content|null
	 */
	public function results(): ?Content {
		if ($this->getMetaValue('notFound')) {
			return null;
		}

		return parent::results() ?? $this->getContent();
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
