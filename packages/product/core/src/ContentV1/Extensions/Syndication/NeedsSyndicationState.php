<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates that a message needs the current state of syndication for the given ID.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
interface NeedsSyndicationState {
	/**
	 * Get the ID of the content in question.
	 *
	 * @return Identifier
	 */
	public function getContentId(): Identifier;

	/**
	 * Store the current state of Syndication on this content.
	 *
	 * @param Syndication $state Current Syndication info.
	 * @return void
	 */
	public function setSyndicationState(Syndication $state);
}
