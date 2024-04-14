<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\Api\ArrayType;
use Smolblog\Foundation\Value;

/**
 * A link property on a Webfinger response.
 */
readonly class WebfingerLink extends Value {
	/**
	 * Undocumented function
	 *
	 * @param string      $rel        Relation to the Webfinger subject.
	 * @param string|null $type       Media type of the link.
	 * @param string|null $href       Location of the link.
	 * @param array|null  $titles     Language => Title for the link.
	 * @param array|null  $properties Key-value metadata.
	 */
	public function __construct(
		public readonly string $rel,
		public readonly ?string $type = null,
		public readonly ?string $href = null,
		#[ArrayType('object')] public readonly ?array $titles = null,
		#[ArrayType('object')] public readonly ?array $properties = null,
	) {
	}

	/**
	 * Serialize this object.
	 *
	 * @return array
	 */
	public function serializeValue(): array {
		return array_filter(parent::serializeValue(), fn($item) => isset($item));
	}
}
