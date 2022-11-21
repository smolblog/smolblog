<?php

namespace Smolblog\Core\Post;

use Smolblog\Framework\Entity;
use Smolblog\Framework\Identifier;

/**
 * A piece of media (image, video, etc) that can be included in a Post.
 */
class Media extends Entity {
	/**
	 * Create the media object
	 *
	 * @param string          $url             URL of the media appropriate for a src attribute.
	 * @param string          $descriptiveText Text description of the media (alt text).
	 * @param array           $attributes      Any additional attributes needed.
	 * @param Identifier|null $id              ID of the media; null if not loaded into DB yet.
	 */
	public function __construct(
		public readonly string $url,
		public readonly string $descriptiveText,
		public readonly array $attributes,
		Identifier $id = null,
	) {
		parent::__construct(id: $id ?? Identifier::createFromDate());
	}
}
