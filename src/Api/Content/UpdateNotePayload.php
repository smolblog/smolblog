<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Value;

/**
 * Composed payload to update a note.
 */
class UpdateNotePayload extends Value {
	/**
	 * Construct the payload. Omit vales for no changes.
	 *
	 * @throws BadRequest No updated attributes provided.
	 *
	 * @param string|null                $text             Updated note text.
	 * @param BaseAttributesPayload|null $baseAttributes   Updated base attributes.
	 * @param SetTagsPayload|null        $tags             Updated tags.
	 * @param SyndicationPayload|null    $syndicationLinks Updated links.
	 * @param ContentVisibility|null     $visibility       Updated visibility.
	 */
	public function __construct(
		public readonly ?string $text = null,
		public readonly ?BaseAttributesPayload $baseAttributes = null,
		public readonly ?SetTagsPayload $tags = null,
		public readonly ?SyndicationPayload $syndicationLinks = null,
		public readonly ?ContentVisibility $visibility = null,
	) {
		if (
			!isset($text) &&
			!isset($baseAttributes) &&
			!isset($tags) &&
			!isset($syndicationLinks) &&
			!isset($visibility)
		) {
			throw new BadRequest('No updated attributes provided.');
		}
	}
}
