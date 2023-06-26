<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Objects\Identifier;

/**
 * A query that finds content then passes to a builder query.
 *
 * The idea is that the data layer can find the content ID and type however it does. Once that is set, the
 * ContentService can take than info and fire off the appropriate *ById query. This allows a single query to return
 * any content type.
 */
abstract class AdaptableContentQuery extends MemoizableQuery {
	/**
	 * ID of the found content.
	 *
	 * @var Identifier
	 */
	private ?Identifier $contentId = null;

	/**
	 * Type of the found content.
	 *
	 * @var string
	 */
	private string $contentType = '';

	/**
	 * Set the info for the found content.
	 *
	 * @param Identifier $id   ID of the content.
	 * @param string     $type Type of the content.
	 * @return void
	 */
	public function setContentInfo(Identifier $id, string $type): void {
		$this->contentId = $id;
		$this->contentType = $type;
	}

	/**
	 * Get the found content's ID.
	 *
	 * @return Identifier|null
	 */
	public function getContentId(): ?Identifier {
		return $this->contentId;
	}

	/**
	 * Get the found content's type.
	 *
	 * @return string
	 */
	public function getContentType(): string {
		return $this->contentType;
	}

	/**
	 * Get the Site being searched.
	 *
	 * @return Identifier
	 */
	abstract public function getSiteId(): Identifier;

	/**
	 * Get the User performing the query. Null if not applicable.
	 *
	 * @return Identifier|null
	 */
	public function getUserId(): ?Identifier {
		return null;
	}
}
