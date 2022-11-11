<?php

namespace Smolblog\Core\Post;

use Smolblog\Core\Entity\Entity;

/**
 * A piece of media (image, video, etc) that can be included in a Post.
 */
class Media extends Entity {
	/**
	 * Create the media object
	 *
	 * @param string       $url             URL of the media appropriate for a src attribute.
	 * @param string       $descriptiveText Text description of the media (alt text).
	 * @param array        $attributes      Any additional attributes needed.
	 * @param integer|null $id              ID of the media; null if not loaded into DB yet.
	 */
	public function __construct(
		public readonly string $url,
		public readonly string $descriptiveText,
		public readonly array $attributes,
		?int $id = null,
	) {
		parent::__construct(id: $id ?? 0);
	}
}
