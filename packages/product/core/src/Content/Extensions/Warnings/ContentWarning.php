<?php

namespace Smolblog\Core\Content\Extensions\Warnings;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * An individual content warning. Can be tagged as a "mention" for minor instances.
 */
readonly class ContentWarning extends Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Construct the warning.
	 *
	 * @param string  $content Description of the content.
	 * @param boolean $mention True if the described content is only mentioned, not depicted.
	 */
	public function __construct(
		public string $content,
		public bool $mention = false,
	) {
	}
}
