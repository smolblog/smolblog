<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Value;

/**
 * Composed payload to update a reblog.
 */
class UpdateReblogPayload extends Value {
	/**
	 * Construct the payload. Omit vales for no changes.
	 *
	 * @throws BadRequest No updated attributes provided.
	 *
	 * @param BaseReblogPayload|null       $reblog           Updated reblog info.
	 * @param BaseAttributesPayload|null   $baseAttributes   Updated base attributes.
	 * @param SetTagsPayload|null          $tags             Updated tags.
	 * @param SyndicationLinksPayload|null $syndicationLinks Updated links.
	 * @param ContentVisibility|null       $visibility       Updated visibility.
	 */
	public function __construct(
		public readonly ?BaseReblogPayload $reblog = null,
		public readonly ?BaseAttributesPayload $baseAttributes = null,
		public readonly ?SetTagsPayload $tags = null,
		public readonly ?SyndicationLinksPayload $syndicationLinks = null,
		public readonly ?ContentVisibility $visibility = null,
	) {
		if (
			!isset($reblog) &&
			!isset($baseAttributes) &&
			!isset($tags) &&
			!isset($syndicationLinks) &&
			!isset($visibility)
		) {
			throw new BadRequest('No updated attributes provided.');
		}
	}
}
