<?php

namespace Smolblog\Core\Content\Extensions\Warnings;

use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * An individual content warning. Can be tagged as a "mention" for minor instances.
 */
readonly class ContentWarning implements Value {
	use ValueKit;

	/**
	 * Construct the warning.
	 *
	 * @param string  $content Description of the content.
	 * @param boolean $mention True if the described content is only mentioned, not depicted.
	 */
	public function __construct(
		public string $content,
		public bool $mention = false,
	) {}
}
