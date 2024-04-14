<?php

namespace Smolblog\Api\Content;

use DateTimeInterface;
use Smolblog\Framework\Objects\ExtendableValueKit;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value;

/**
 * Payload for content base attributes.
 */
readonly class ContentTypePayload extends Value {
	use ExtendableValueKit;

	/**
	 * Construct the payload
	 *
	 * @param string $type     Key for the content type this represents.
	 * @param mixed  ...$props Properties of the content.
	 */
	public function __construct(
		public string $type,
		mixed ...$props,
	) {
		$this->extendedFields = $props;
	}
}
